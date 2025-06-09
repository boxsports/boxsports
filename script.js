const player = videojs('videoPlayer');
const channelsContainer = document.getElementById('channels');
const searchInput = document.getElementById('search');

// Örnek JSON formatında kanal listesi
const channels = [
  { name: 'BEIN SPORTS 1', url: 'https://example.com/bein1.m3u8' },
  { name: 'BEIN SPORTS 2', url: 'https://example.com/bein2.m3u8' },
  { name: 'BEIN SPORTS 3', url: 'https://example.com/bein3.m3u8' },
  { name: 'BEIN SPORTS 4', url: 'https://example.com/bein4.m3u8' },
  { name: 'BEIN SPORTS 5', url: 'https://example.com/bein5.m3u8' },
  { name: 'BEIN SPORTS MAX 1', url: 'https://example.com/beinmax1.m3u8' },
  { name: 'BEIN SPORTS MAX 2', url: 'https://example.com/beinmax2.m3u8' },
  { name: 'S SPORT', url: 'https://example.com/ssport.m3u8' }
];

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

// İlk kanal yüklensin
window.onload = () => {
  loadChannels();
  player.src({ type: 'application/x-mpegURL', src: channels[0].url });
  player.play();
};
