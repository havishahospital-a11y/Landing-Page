// Only the server-rendered confirmation, reached after persistence, has this script.
const link = document.querySelector('#whatsapp-link');
if (link && !sessionStorage.getItem('whatsapp:' + link.href)) {
  sessionStorage.setItem('whatsapp:' + link.href, '1');
  setTimeout(() => { window.location.assign(link.href); }, 1800);
}
