document.addEventListener("DOMContentLoaded", function () {
  fetch("channels.json")
    .then((response) => response.json())
    .then((channels) => {
      const channelList = document.getElementById("channels");
      const videoPlayer = document.getElementById("videoPlayer");
      const searchInput = document.getElementById("searchInput");

      function renderList(filter = "") {
        channelList.innerHTML = "";
        channels
          .filter((ch) =>
            ch.name.toLowerCase().includes(filter.toLowerCase())
          )
          .forEach((channel) => {
            const li = document.createElement("li");
            li.textContent = channel.name + " • Canlı";
            li.onclick = () => {
              videoPlayer.src = channel.url;
            };
            channelList.appendChild(li);
          });
      }

      renderList();

      searchInput.addEventListener("input", (e) => {
        renderList(e.target.value);
      });
    })
    .catch((err) => {
      console.error("Kanal verisi yüklenemedi:", err);
    });
});
