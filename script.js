function playChannel(src) {
  const player = document.getElementById("videoPlayer");
  player.src = src;
  player.play();
}
