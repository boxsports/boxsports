<?php
// kanal.json dosyasını oku ve decode et
$channelsJson = file_get_contents(__DIR__ . '/kanal.json');
$channels = json_decode($channelsJson, true);

// Eğer dosya okunamadıysa boş dizi olsun
if (!$channels) {
    $channels = [];
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>BOX SPORTS - Canlı Spor Yayını</title>

  <!-- Favicon -->
  <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" type="image/png" />

  <!-- HLS.js CDN -->
  <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />

  <style>
    /* Buraya senin verdiğin tüm CSS kodları gelecek, aynen yapıştırabilirsin */
    /* ... Senin verdiğin tüm CSS kodları ... */
    :root {
      --color-bg-light: #e6f7ff;
      --color-bg-dark: #001f3f;
      --color-primary-light: #0099ff;
      --color-primary-dark: #3399ff;
      --color-text-light: #0a2540;
      --color-text-dark: #cce6ff;
      --color-status: #28a745;
      --color-white: #fff;
      --color-accent: #ff9800;
    }
    [data-theme="light"] {
      --bg: var(--color-bg-light);
      --text: var(--color-text-light);
      --primary: var(--color-primary-light);
      --status: var(--color-status);
      --white: var(--color-white);
      --accent: var(--color-accent);
    }
    [data-theme="dark"] {
      --bg: var(--color-bg-dark);
      --text: var(--color-text-dark);
      --primary: var(--color-primary-dark);
      --status: var(--color-status);
      --white: var(--color-white);
      --accent: var(--color-accent);
    }
    *, *::before, *::after {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      font-family: 'Inter', Arial, sans-serif;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      transition: background-color 0.3s ease, color 0.3s ease;
      user-select: none;
    }
    header {
      background-color: var(--primary);
      color: var(--white);
      padding: 1.5rem 1rem;
      font-size: 1.75rem;
      font-weight: 700;
      text-align: center;
      box-shadow: 0 3px 6px rgba(0,0,0,0.2);
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
    }
    header img {
      height: 36px;
      filter: drop-shadow(0 0 1px rgba(0,0,0,0.3));
      cursor: pointer;
    }
    main.container {
      flex: 1;
      max-width: 1200px;
      margin: 1rem auto;
      width: 100%;
      display: flex;
      gap: 1.5rem;
      padding: 0 1rem;
      box-sizing: border-box;
    }
    .channel-list {
      width: 320px;
      background: var(--white);
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.12);
      display: flex;
      flex-direction: column;
      overflow: hidden;
    }
    .channel-list input,
    .channel-list select {
      border: none;
      border-bottom: 2px solid var(--primary);
      padding: 0.75rem 1rem;
      font-size: 1rem;
      outline: none;
      transition: border-color 0.3s ease;
      background: #fff;
      color: #000;
    }
    .channel-list input:focus,
    .channel-list select:focus {
      border-color: var(--accent);
    }
    #channelContainer {
      flex: 1;
      overflow-y: auto;
      padding: 0.5rem 0;
      scrollbar-width: thin;
      scrollbar-color: var(--primary) #e0e0e0;
    }
    #channelContainer::-webkit-scrollbar {
      width: 8px;
    }
    #channelContainer::-webkit-scrollbar-thumb {
      background-color: var(--primary);
      border-radius: 10px;
    }
    #channelContainer::-webkit-scrollbar-track {
      background: #e0e0e0;
      border-radius: 10px;
    }
    .channel {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 1.25rem;
      border-bottom: 1px solid #f0f4f8;
      font-weight: 600;
      color: #004a99;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    .channel:last-child {
      border-bottom: none;
    }
    .channel:hover {
      background-color: #e6f4ff;
    }
    .channel.active {
      background-color: var(--primary);
      color: var(--white);
      font-weight: 700;
      box-shadow: 0 0 12px #3399ffaa;
    }
    .status {
      font-size: 0.85rem;
      font-weight: 700;
      color: var(--status);
      user-select: none;
      white-space: nowrap;
      margin-left: 0.5rem;
    }
    .video-player {
      flex: 1;
      border-radius: 16px;
      overflow: hidden;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      background: black;
      position: relative;
      min-height: 360px;
      display: flex;
      flex-direction: column;
    }
    video#video {
      width: 100%;
      height: 100%;
      background: black;
      outline: none;
      flex: 1;
    }
    footer {
      text-align: center;
      padding: 1rem;
      font-size: 0.9rem;
      background: var(--bg);
      color: var(--text);
      user-select: none;
      box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
    }
    /* Tema butonu */
    .theme-toggle {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: var(--white);
      border: none;
      border-radius: 20px;
      cursor: pointer;
      width: 42px;
      height: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: background-color 0.3s ease;
      box-shadow: 0 0 8px #0003;
    }
    .theme-toggle:hover {
      background: var(--accent);
      color: var(--white);
    }
    .theme-toggle svg {
      width: 18px;
      height: 18px;
      fill: var(--primary);
      transition: fill 0.3s ease;
    }
    /* Dil seçici */
    .lang-select {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: var(--white);
      border: none;
      border-radius: 8px;
      padding: 4px 10px;
      font-weight: 600;
      cursor: pointer;
      color: var(--primary);
      box-shadow: 0 0 6px #0002;
      transition: background-color 0.3s ease;
    }
    .lang-select:hover {
      background: var(--accent);
      color: var(--white);
    }
    /* Saat göstergesi */
    .clock {
      position: absolute;
      bottom: 6px;
      left: 50%;
      transform: translateX(-50%);
      font-size: 0.85rem;
      font-weight: 600;
      color: var(--white);
      user-select: none;
      font-family: monospace;
      letter-spacing: 1px;
    }
    /* Responsive */
    @media (max-width: 900px) {
      main.container {
        flex-direction: column;
      }
      .channel-list {
        width: 100%;
        max-height: 200px;
        overflow-y: auto;
        border-radius: 0;
      }
      .video-player {
        min-height: 280px;
      }
      header {
        font-size: 1.5rem;
      }
      .theme-toggle {
        top: auto;
        bottom: 6px;
        right: 6px;
        transform: none;
      }
      .lang-select {
        top: auto;
        bottom: 6px;
        left: 6px;
        transform: none;
      }
      .clock {
        display: none;
      }
    }
  </style>
</head>
<body>
  <header role="banner" aria-label="Sayfa Başlığı">
    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="Box Sports Logo" title="Box Sports" />
    <span id="header-title">Box Sports</span>

    <select aria-label="Dil seçici" class="lang-select" id="langSelect" title="Dil Seç">
      <option value="tr">Türkçe</option>
      <option value="en">English</option>
    </select>

    <button aria-label="Tema değiştirici" id="themeToggle" class="theme-toggle" title="Tema değiştir">
      <svg id="themeIcon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><circle cx="12" cy="12" r="5"/></svg>
    </button>

    <div class="clock" id="clock" aria-live="polite" aria-atomic="true"></div>
  </header>

  <main class="container" role="main">
    <nav class="channel-list" aria-label="Kanal Listesi">
      <input type="text" id="searchInput" placeholder="Kanal Ara..." aria-label="Kanal Ara" />
      <select id="filterCategory" aria-label="Kategori Filtrele" title="Kategori Filtrele">
        <option value="all">Tüm Kategoriler</option>
      </select>
      <div id="channelContainer" tabindex="0"></div>
    </nav>

    <section class="video-player" aria-label="Video Oynatıcı">
      <video id="video" controls autoplay playsinline></video>
    </section>
  </main>

  <footer>
    &copy; 2025 Box Sports | Tüm Hakları Saklıdır.
  </footer>

  <script>
    "use strict";

    // PHP’den gelen kanalları JS’ye aktaralım
    const channels = <?php echo json_encode($channels, JSON_UNESCAPED_UNICODE); ?>;

    // Çoklu dil metinleri
    const langData = {
      tr: {
        searchPlaceholder: "Kanal Ara...",
        allCategories: "Tüm Kategoriler",
        liveStatus: "Canlı",
        offlineStatus: "Çevrimdışı",
        errorLoadChannels: "Kanallar yüklenemedi.",
        errorLoadStream: "Yayın yüklenemedi.",
        headerTitle: "Box Sports",
      },
      en: {
        searchPlaceholder: "Search channel...",
        allCategories: "All Categories",
        liveStatus: "Live",
        offlineStatus: "Offline",
        errorLoadChannels: "Failed to load channels.",
        errorLoadStream: "Failed to load stream.",
        headerTitle: "Box Sports",
      }
    };

    // Elements
    const channelContainer = document.getElementById('channelContainer');
    const searchInput = document.getElementById('searchInput');
    const filterCategory = document.getElementById('filterCategory');
    const video = document.getElementById('video');
    const themeToggle = document.getElementById('themeToggle');
    const langSelect = document.getElementById('langSelect');
    const headerTitle = document.getElementById('header-title');
    const clock = document.getElementById('clock');

    // Aktif kanal indeksi
    let activeChannelIndex = null;

    // Temayı kaydet / yükle
    function loadTheme() {
      const saved = localStorage.getItem('theme') || 'light';
      document.documentElement.setAttribute('data-theme', saved);
      updateThemeIcon(saved);
    }
    function toggleTheme() {
      const current = document.documentElement.getAttribute('data-theme');
      const next = current === 'light' ? 'dark' : 'light';
      document.documentElement.setAttribute('data-theme', next);
      localStorage.setItem('theme', next);
      updateThemeIcon(next);
    }
    function updateThemeIcon(theme) {
      const svg = document.getElementById('themeIcon');
      if (theme === 'dark') {
        svg.innerHTML = '<circle cx="12" cy="12" r="5" fill="yellow"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" stroke="yellow" stroke-width="2" stroke-linecap="round"/>';
      } else {
        svg.innerHTML = '<circle cx="12" cy="12" r="5" fill="black"/>';
      }
    }

    // Dil değişimi
    function updateLanguage(lang) {
      const data = langData[lang] || langData.tr;
      searchInput.placeholder = data.searchPlaceholder;
      filterCategory.options[0].text = data.allCategories;
      headerTitle.textContent = data.headerTitle;
      renderChannels(); // metin güncellenince yeniden renderla
    }

    // Saat güncelle
    function updateClock() {
      const now = new Date();
      const h = now.getHours().toString().padStart(2,'0');
      const m = now.getMinutes().toString().padStart(2,'0');
      const s = now.getSeconds().toString().padStart(2,'0');
      clock.textContent = `${h}:${m}:${s}`;
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Kategorileri kanal listesinden alıp filtre dropdown’una ekle
    function populateCategories() {
      const categories = new Set();
      channels.forEach(ch => {
        if (ch.category) categories.add(ch.category);
      });
      const arr = Array.from(categories).sort();
      arr.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat;
        option.textContent = cat;
        filterCategory.appendChild(option);
      });
    }

    // Kanalları renderla
    function renderChannels() {
      const lang = langSelect.value || 'tr';
      const data = langData[lang];
      const searchTerm = searchInput.value.toLowerCase();
      const selectedCategory = filterCategory.value;

      channelContainer.innerHTML = '';

      let filteredChannels = channels;

      if (selectedCategory !== 'all') {
        filteredChannels = filteredChannels.filter(ch => ch.category === selectedCategory);
      }
      if (searchTerm) {
        filteredChannels = filteredChannels.filter(ch => ch.name.toLowerCase().includes(searchTerm));
      }

      if (filteredChannels.length === 0) {
        const noData = document.createElement('div');
        noData.textContent = data.errorLoadChannels;
        noData.style.padding = '1rem';
        channelContainer.appendChild(noData);
        return;
      }

      filteredChannels.forEach((channel, i) => {
        const chElem = document.createElement('div');
        chElem.classList.add('channel');
        if (channels.indexOf(channel) === activeChannelIndex) {
          chElem.classList.add('active');
        }
        chElem.tabIndex = 0;

        const chName = document.createElement('span');
        chName.textContent = channel.name;

        const status = document.createElement('span');
        status.classList.add('status');
        status.textContent = channel.live ? data.liveStatus : data.offlineStatus;

        chElem.appendChild(chName);
        chElem.appendChild(status);

        chElem.addEventListener('click', () => {
          selectChannel(channels.indexOf(channel));
        });
        chElem.addEventListener('keydown', (e) => {
          if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            selectChannel(channels.indexOf(channel));
          }
        });

        channelContainer.appendChild(chElem);
      });
    }

    // Kanal seçme ve video oynatma
    function selectChannel(index) {
      if (index === activeChannelIndex) return;
      activeChannelIndex = index;
      renderChannels();

      const channel = channels[index];
      if (!channel) return;

      const streamUrl = channel.stream_url;
      if (!streamUrl) {
        alert(langData[langSelect.value].errorLoadStream);
        return;
      }

      // HLS.js ile oynatma
      if (Hls.isSupported()) {
        if (window.hls) {
          window.hls.destroy();
        }
        window.hls = new Hls();
        window.hls.loadSource(streamUrl);
        window.hls.attachMedia(video);
        window.hls.on(Hls.Events.ERROR, function(event, data) {
          if (data.fatal) {
            alert(langData[langSelect.value].errorLoadStream);
          }
        });
      } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = streamUrl;
      } else {
        alert(langData[langSelect.value].errorLoadStream);
      }

      video.play().catch(() => {});
    }

    // Event listener'lar
    searchInput.addEventListener('input', renderChannels);
    filterCategory.addEventListener('change', renderChannels);
    themeToggle.addEventListener('click', toggleTheme);
    langSelect.addEventListener('change', () => {
      updateLanguage(langSelect.value);
      if (activeChannelIndex !== null) {
        selectChannel(activeChannelIndex);
      }
    });

    // Sayfa yüklendiğinde
    window.addEventListener('load', () => {
      loadTheme();
      populateCategories();
      updateLanguage(langSelect.value);
      renderChannels();

      // Otomatik olarak ilk canlı kanalı seç
      const firstLiveIndex = channels.findIndex(ch => ch.live);
      selectChannel(firstLiveIndex >= 0 ? firstLiveIndex : 0);
    });
  </script>
</body>
</html>
