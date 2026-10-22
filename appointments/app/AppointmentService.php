<?php
declare(strict_types=1);
final class AppointmentService
{
    public function __construct(private Repository $repository, private ?CrmClient $crm = null)
    {
        $this->crm ??= new CrmClient();
    }
    public function create(array $data, string $key, string $origin, array $attribution): array
    {
        $booking = $this->repository->save($data, $key, $origin, $attribution);
        $this->crm->deliver($booking);
        return $booking;
    }
    public function sync(array $booking): array
    {
        $this->crm->deliver($booking);
        return $booking;
    }
    public static function whatsapp(array $booking, array $branches): string
    {
        $branch = $branches[$booking['branch']];
        $message = 'Hello Havisha Hospital, I submitted an appointment request for ' . $branch['name'] . '. My reference is ' . $booking['public_reference'] . '. My preferred date is ' . $booking['preferred_date'] . '. Please help confirm my appointment.';
        return 'https://wa.me/' . $branch['whatsapp'] . '?text=' . rawurlencode($message);
    }
}
