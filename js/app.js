document.addEventListener("DOMContentLoaded", () => {
  const channelList = document.getElementById("channelList");
  const player = document.getElementById("player");
  const searchInput = document.getElementById("searchInput");
  const clock = document.getElementById("clock");

  function updateClock() {
    const now = new Date();
    clock.textContent = now.toLocaleTimeString();
  }
  setInterval(updateClock, 1000);
  updateClock();

  fetch("kanal.json")
    .then((response) => response.json())
    .then((channels) => {
      function renderChannels(filter = "") {
        channelList.innerHTML = "";
        channels
          .filter((ch) => ch.name.toLowerCase().includes(filter.toLowerCase()))
          .forEach((channel) => {
            const div = document.createElement("div");
            div.textContent = `${channel.name} - ${channel.category}`;
            div.onclick = () => {
              player.src = channel.url;
              player.play();
            };
            channelList.appendChild(div);
          });
      }

      searchInput.addEventListener("input", () =>
        renderChannels(searchInput.value)
      );
      renderChannels();
    });
});