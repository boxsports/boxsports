document.addEventListener("DOMContentLoaded", () => {
  fetch("m3u.json")
    .then(response => response.json())
    .then(data => {
      const channelList = document.getElementById("channelList");
      const searchInput = document.getElementById("searchInput");
      const videoPlayer = document.getElementById("videoPlayer");

      function renderList(filter = "") {
        channelList.innerHTML = "";
        data.filter(channel => channel.name.toLowerCase().includes(filter.toLowerCase()))
            .forEach(channel => {
              const li = document.createElement("li");
              li.textContent = `${channel.name} • Canlı`;
              li.onclick = () => {
                videoPlayer.src = channel.url;
                let clicks = parseInt(localStorage.getItem("clicks") || "0");
                localStorage.setItem("clicks", clicks + 1);
              };
              channelList.appendChild(li);
            });
      }

      renderList();

      searchInput.addEventListener("input", () => {
        renderList(searchInput.value);
      });
    });

  // F12 ve Inspect engelleme
  document.onkeydown = function (e) {
    if (e.key === "F12" || (e.ctrlKey && e.shiftKey && (e.key === "I" || e.key === "J")) || (e.ctrlKey && e.key === "U")) {
      return false;
    }
  };
});
