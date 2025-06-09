const player = videojs('videoPlayer');
const channelsContainer = document.getElementById('channels');
const searchInput = document.getElementById('search');

let channels = []; // JSON'dan yüklenecek

function loadChannels(filter = '') {
  channelsContainer.innerHTML = '';
  channels
    .filter(c => c.name.toLowerCase().includes(filter.toLowerCase()))
    .forEach(channel => {
      const div = document.createElement('div');
      div.className = 'channel';
      div.innerHTML = `<span>${channel.name}</span><span class="status">● Canlı</span>`;
      div.onclick = () => {
        player.src({ type: 'application/x-mpegURL', src: channel.url });
        player.play();
      };
      channelsContainer.appendChild(div);
    });
}

searchInput.addEventListener('input', e => loadChannels(e.target.value));

window.onload = () => {
  fetch('channels.json')
    .then(response => response.json())
    .then(data => {
      channels = data;
      loadChannels();
      if (channels.length > 0) {
        player.src({ type: 'application/x-mpegURL', src: channels[0].url });
        player.play();
      }
    })
    .catch(error => {
      console.error("Kanal listesi yüklenemedi:", error);
      channelsContainer.innerHTML = "<p>Kanallar yüklenemedi.</p>";
    });
};
