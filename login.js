(() => {
  const toast = document.querySelector("#toast");

  if (!toast || new URLSearchParams(location.search).get("error") !== "1")
    return;

  toast.hidden = false;
  requestAnimationFrame(() => toast.classList.add("show"));

  setTimeout(() => {
    toast.classList.remove("show");
    history.replaceState(null, document.title, location.pathname);
    setTimeout(() => {
      toast.hidden = true;
    }, 300);
  }, 3000);
})();
