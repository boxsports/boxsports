const channelList = document.getElementById("channelList");
const searchInput = document.getElementById("searchInput");
const videoPlayer = document.getElementById("videoPlayer");
let hls;

function loadChannels(filter = "") {
  channelList.innerHTML = "";
  channels
    .filter(ch => ch.name.toLowerCase().includes(filter.toLowerCase()))
    .forEach(ch => {
      const li = document.createElement("li");
      li.innerHTML = `${ch.name} <span class="tag">● Canlı</span>`;
      li.addEventListener("click", () => loadStream(ch.url));
      channelList.appendChild(li);
    });
}

function loadStream(url) {
  if (Hls.isSupported()) {
    if (hls) hls.destroy();
    hls = new Hls();
    hls.loadSource(url);
    hls.attachMedia(videoPlayer);
  } else if (videoPlayer.canPlayType("application/vnd.apple.mpegurl")) {
    videoPlayer.src = url;
  }
}

searchInput.addEventListener("input", e => {
  loadChannels(e.target.value);
});

window.addEventListener("DOMContentLoaded", () => {
  loadChannels();
});
