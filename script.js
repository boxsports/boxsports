const channels = [
  { name: "BEIN SPORTS 1", url: "https://lll.istekbet3.tv/yayinzirve.m3u8" },
  { name: "BEIN SPORTS 2", url: "https://example.com/stream2" },
  { name: "BEIN SPORTS 3", url: "https://example.com/stream3" },
  { name: "BEIN SPORTS 4", url: "https://example.com/stream4" },
  { name: "BEIN SPORTS 5", url: "https://example.com/stream5" },
  { name: "BEIN SPORTS MAX 1", url: "https://example.com/stream6" },
  { name: "BEIN SPORTS MAX 2", url: "https://example.com/stream7" },
  { name: "S SPORT", url: "https://example.com/stream8" },
];

const list = document.getElementById('channelItems');
const search = document.getElementById('search');
const player = document.getElementById('player');

function renderChannels(filter = '') {
  list.innerHTML = '';
  channels
    .filter(c => c.name.toLowerCase().includes(filter.toLowerCase()))
    .forEach(c => {
      const li = document.createElement('li');
      li.innerHTML = `${c.name} <i class="fa-solid fa-circle-play"></i>`;
      li.onclick = () => {
        player.src = c.url;
      };
      list.appendChild(li);
    });
}

search.addEventListener('input', (e) => {
  renderChannels(e.target.value);
});

renderChannels();
