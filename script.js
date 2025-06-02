fetch('channels.json')
  .then(res => res.json())
  .then(data => {
    const list = document.getElementById('channel-list');
    data.channels.forEach(channel => {
      const btn = document.createElement('button');
      btn.textContent = channel.name;
      btn.onclick = () => document.getElementById('player').src = channel.url;
      list.appendChild(btn);
    });
  });
