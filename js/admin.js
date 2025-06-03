document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("channelForm");
  const list = document.getElementById("adminChannelList");

  let channels = [];

  fetch("kanal.json")
    .then((res) => res.json())
    .then((data) => {
      channels = data;
      renderList();
    });

  function renderList() {
    list.innerHTML = "";
    channels.forEach((ch, i) => {
      const li = document.createElement("li");
      li.textContent = ch.name + " - " + ch.category;
      li.onclick = () => {
        document.getElementById("name").value = ch.name;
        document.getElementById("category").value = ch.category;
        document.getElementById("url").value = ch.url;
      };
      list.appendChild(li);
    });
  }

  form.onsubmit = (e) => {
    e.preventDefault();
    const name = document.getElementById("name").value;
    const category = document.getElementById("category").value;
    const url = document.getElementById("url").value;

    const index = channels.findIndex((c) => c.name === name);
    if (index > -1) {
      channels[index] = { name, category, url };
    } else {
      channels.push({ name, category, url });
    }
    renderList();
    alert("Kanal güncellendi (kalıcı kayıt için backend gerekir).");
  };
});