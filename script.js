
const channels = [
  { name: "BEIN SPORTS 1", url: "https://example.com/bein1.m3u8" },
  { name: "BEIN SPORTS 2", url: "https://example.com/bein2.m3u8" },
  { name: "BEIN SPORTS 3", url: "https://example.com/bein3.m3u8" },
  { name: "S SPORT", url: "https://example.com/ssport.m3u8" },
  { name: "TRT SPOR", url: "https://example.com/trtspor.m3u8" },
];

const channelList = document.getElementById("channelList");
const search = document.getElementById("search");
const player = document.getElementById("videoPlayer");

let hls;

function loadChannels(filter = "") {
  channelList.innerHTML = "";
  channels
    .filter(c => c.name.toLowerCase().includes(filter.toLowerCase()))
    .forEach((channel, index) => {
      const li = document.createElement("li");
      li.innerHTML = `${channel.name} <span class="live">● Canlı</span>`;
      li.onclick = () => {
        document.querySelectorAll("#channelList li").forEach(el => el.classList.remove("active"));
        li.classList.add("active");
        playStream(channel.url);
      };
      if (index === 0) li.classList.add("active");
      channelList.appendChild(li);
    });
}

function playStream(url) {
  if (hls) {
    hls.destroy();
  }

  if (Hls.isSupported()) {
    hls = new Hls();
    hls.loadSource(url);
    hls.attachMedia(player);
    hls.on(Hls.Events.MANIFEST_PARSED, () => {
      player.play();
    });
  } else if (player.canPlayType("application/vnd.apple.mpegurl")) {
    player.src = url;
    player.play();
  } else {
    alert("Tarayıcınız bu yayını desteklemiyor.");
  }
}

search.addEventListener("input", e => {
  loadChannels(e.target.value);
});

window.onload = () => {
  loadChannels();
  playStream(channels[0].url);
};
