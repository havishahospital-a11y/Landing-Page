<?php
declare(strict_types=1);
final class Validation
{
    public static function dateAllowed(string $date, array $branch, ?DateTimeImmutable $now = null): bool
    {
        $now = $now ?? new DateTimeImmutable('now', new DateTimeZone('Asia/Kolkata'));
        $day = DateTimeImmutable::createFromFormat('!Y-m-d', $date, new DateTimeZone('Asia/Kolkata'));
        if (!$day || $day->format('Y-m-d') !== $date || $date < $now->format('Y-m-d') || in_array($date, $branch['closures'], true))
            return false;
        $hours = $branch['schedule'][(int) $day->format('N')] ?? null;
        return $hours !== null && ($date !== $now->format('Y-m-d') || $now->format('H:i') < $hours[1]);
    }
    public static function phone(string $raw): ?string
    {
        $n = preg_replace('/[\s()\-]/u', '', $raw);
        if (!preg_match('/^(?:\+91|91)?([6-9][0-9]{9})$/D', $n, $m))
            return null;
        return '+91' . $m[1];
    }
    public static function check(array $input, array $branches, array $symptoms): array
    {
        $data = [];
        $errors = [];
        foreach (['name' => 100, 'phone' => 24, 'branch' => 20, 'city' => 100, 'date' => 10, 'other' => 500] as $field => $limit) {
            $value = $input[$field] ?? '';
            $data[$field] = is_string($value) ? trim($value) : '';
            if (!mb_check_encoding($data[$field], 'UTF-8') || mb_strlen($data[$field]) > $limit || preg_match('/[\x00-\x1F\x7F]/u', $data[$field]))
                $errors[$field] = 'Please enter a valid value within ' . $limit . ' characters.';
            if ($field !== 'other' && $data[$field] === '')
                $errors[$field] = 'Please complete this field.';
        }
        if (!self::phone($data['phone']))
            $errors['phone'] = 'Enter a valid 10-digit Indian mobile number, with or without +91.';
        else
            $data['phone'] = self::phone($data['phone']);
        if (!isset($branches[$data['branch']]))
            $errors['branch'] = 'Choose a listed branch.';
        elseif (!self::dateAllowed($data['date'], $branches[$data['branch']]))
            $errors['date'] = 'Choose a consultation date from the available days. Today closes at the end of consultation hours.';
        $selected = $input['symptoms'] ?? [];
        if (!is_array($selected) || !$selected || count($selected) > count($symptoms) || array_filter($selected, fn($s) => !is_string($s) || !isset($symptoms[$s]))) {
            $errors['symptoms'] = 'பட்டியலிலிருந்து குறைந்தது ஓர் அறிகுறியைத் தேர்ந்தெடுக்கவும்.';
            $selected = [];
        }
        $data['symptoms'] = array_values(array_unique($selected));
        if (in_array('other', $selected, true) && $data['other'] === '')
            $errors['other'] = 'உங்கள் மற்ற அறிகுறியை விவரிக்கவும்.';
        if (!in_array('other', $selected, true))
            $data['other'] = '';
        if (($input['consent'] ?? '') !== '1')
            $errors['consent'] = 'Please agree to be contacted about your appointment enquiry.';
        return [$data, $errors];
    }
}
