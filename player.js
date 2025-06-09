
// Kanal listesini yükle ve tıklanınca oynat
fetch('channels.json')
  .then(res => res.json())
  .then(data => {
    const channelList = document.getElementById('channelList');
    const player = videojs('videoPlayer');

    function loadChannel(url) {
      player.src({ src: url, type: 'application/x-mpegURL' });
      player.play();
    }

    data.forEach((channel, index) => {
      const li = document.createElement('li');
      li.innerHTML = `
        <button class="w-full text-left p-3 rounded-lg bg-blue-100 hover:bg-blue-200 transition-all duration-200 shadow-sm">
          <span class="font-semibold">${channel.name}</span>
          <span class="text-green-500 ml-2 font-bold">• Canlı</span>
        </button>
      `;
      li.querySelector('button').onclick = () => loadChannel(channel.url);
      channelList.appendChild(li);
      if (index === 0) loadChannel(channel.url); // İlk kanal varsayılan yüklensin
    });

    document.getElementById('searchInput').addEventListener('input', function() {
      const val = this.value.toLowerCase();
      [...channelList.children].forEach(li => {
        li.style.display = li.innerText.toLowerCase().includes(val) ? 'block' : 'none';
      });
    });
  });
