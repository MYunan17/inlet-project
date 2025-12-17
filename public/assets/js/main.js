// =========================================================================
// JQUERY: LIVE SEARCH & FILTER
// =========================================================================
$(document).ready(function () {
  // 1. Live Search (Team/Member)
  $("#team-search").on("keyup", function () {
    var searchTerm = $(this).val().toLowerCase();

    $(".member-grid .member-card").each(function () {
      var memberText = $(this).find(".member-info").text().toLowerCase();
      var memberRoleAttr = $(this).data("role").toLowerCase();

      if (
        memberText.includes(searchTerm) ||
        memberRoleAttr.includes(searchTerm)
      ) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });

    if (searchTerm.length > 0) $(".pagination-container").hide();
    else $(".pagination-container").show();
  });

  // 2. Filter Button (Member)
  $(".btn-filter").on("click", function () {
    var role = $(this).data("role");

    $(".btn-filter").removeClass("active");
    $(this).addClass("active");

    if (role !== "all") $(".pagination-container").hide();
    else $(".pagination-container").show();

    $(".member-grid .member-card").each(function () {
      var memberRole = $(this).data("role");
      if (role === "all" || memberRole === role) $(this).show();
      else $(this).hide();
    });

    $("#team-search").val("");
  });
});

// =========================================================================
// JS UTAMA: NAVBAR, SCROLL, LIGHTBOX, LOAD MORE
// =========================================================================
document.addEventListener("DOMContentLoaded", () => {
  const navbar = document.querySelector(".navbar");
  const toggleButton = document.querySelector(".hamburger-toggle");
  const menuLinks = document.querySelector("#menu-links");
  const scrollThreshold = 80;

  // Lightbox Elements
  const lightboxOverlay = document.getElementById("lightbox-overlay");
  const lightboxClose = document.querySelector(".lightbox-close");
  const lightboxMediaContainer = document.getElementById("lightbox-media");

  // Load More Elements
  const btnLoadMore = document.getElementById("btn-load-more");
  const galleryGrid = document.querySelector(".photo-grid");

  const baseUrl = window.AppConfig.BASE_URL;
  const loadMoreUrl = `${baseUrl}gallery/loadMore`;
  let currentOffset = window.AppConfig.GALLERY.INITIAL_LIMIT;
  const limit = window.AppConfig.GALLERY.INITIAL_LIMIT;
  const totalItems = window.AppConfig.GALLERY.TOTAL_ITEMS;

  // =========================
  // NAVBAR SCROLL & TOGGLE
  // =========================
  if (toggleButton && menuLinks) {
    const toggleIcon = toggleButton.querySelector("i");
    toggleButton.addEventListener("click", () => {
      menuLinks.classList.toggle("active");
      if (menuLinks.classList.contains("active")) {
        toggleIcon.textContent = "close";
        toggleIcon.classList.add("rotated");
      } else {
        toggleIcon.textContent = "menu";
        toggleIcon.classList.remove("rotated");
      }
      const isExpanded =
        toggleButton.getAttribute("aria-expanded") === "true" || false;
      toggleButton.setAttribute("aria-expanded", !isExpanded);
    });
  }

  if (navbar) {
    const handleScroll = () => {
      if (window.innerWidth >= 768) {
        if (window.scrollY > scrollThreshold) navbar.classList.add("scrolled");
        else navbar.classList.remove("scrolled");
      } else navbar.classList.remove("scrolled");
    };
    window.addEventListener("scroll", handleScroll);
    window.addEventListener("resize", handleScroll);
    handleScroll();
  }

  // =========================
  // LIGHTBOX FUNCTIONS
  // =========================
  function closeLightbox() {
    const mediaElement = lightboxMediaContainer.querySelector("video");
    if (mediaElement) {
      mediaElement.pause();
      mediaElement.currentTime = 0;
    }
    lightboxOverlay.classList.remove("active");
    document.body.style.overflow = "";
    lightboxMediaContainer.innerHTML = "";
  }

  function openLightbox(item) {
    const type = item.dataset.type;
    const fileUrl = item.dataset.target;

    lightboxMediaContainer.innerHTML = "";

    if (type === "photo") {
      const img = document.createElement("img");
      img.src = fileUrl.startsWith("http")
        ? fileUrl
        : `${baseUrl}assets/images/gallery/${fileUrl}`;
      lightboxMediaContainer.appendChild(img);
    } else if (type === "video") {
      const video = document.createElement("video");
      video.controls = true;
      video.autoplay = false; // autoplay bisa diblok browser
      video.preload = "metadata";
      video.src = fileUrl.startsWith("http")
        ? fileUrl
        : `${baseUrl}assets/videos/gallery/${fileUrl}`;
      lightboxMediaContainer.appendChild(video);
    }

    lightboxOverlay.classList.add("active");
    document.body.style.overflow = "hidden";
  }

  if (lightboxClose) lightboxClose.addEventListener("click", closeLightbox);
  if (lightboxOverlay) {
    lightboxOverlay.addEventListener("click", (e) => {
      if (e.target === lightboxOverlay) closeLightbox();
    });
  }
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && lightboxOverlay.classList.contains("active")) {
      closeLightbox();
    }
  });

  // =========================
  // EVENT DELEGATION: GALLERY ITEMS
  // =========================
  if (galleryGrid) {
    galleryGrid.addEventListener("click", (e) => {
      const item = e.target.closest(".gallery-item");
      if (item) openLightbox(item);
    });
  }

  // =========================
  // LOAD MORE FUNCTION
  // =========================
  if (btnLoadMore && galleryGrid) {
    // Sembunyikan tombol jika semua sudah dimuat
    if (currentOffset >= totalItems) btnLoadMore.style.display = "none";

    function createGalleryItem(item) {
      const type = item.type || "photo";
      const title = item.title || "Media Tanpa Judul";
      const thumbnail = item.thumbnail_url || "";
      const fileUrl = item.file_url || "";

      let html = `<div class="gallery-item" data-type="${type}" data-target="${fileUrl}">`;
      html += `<img src="${baseUrl}assets/images/gallery/${thumbnail}" alt="${title}">`;
      if (type === "video")
        html += `<i class="material-icons-outlined play-icon">play_circle_filled</i>`;
      html += `<p class="caption">${title}</p></div>`;
      return html;
    }

    function loadMoreGallery() {
      btnLoadMore.disabled = true;
      btnLoadMore.textContent = "Memuat...";

      fetch(`${loadMoreUrl}?limit=${limit}&offset=${currentOffset}`)
        .then((res) => {
          if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
          return res.json();
        })
        .then((data) => {
          if (data.success && data.items.length > 0) {
            const newHtml = data.items
              .map((item) => createGalleryItem(item))
              .join("");
            galleryGrid.insertAdjacentHTML("beforeend", newHtml);
            currentOffset += data.items.length;

            // Sembunyikan tombol jika semua sudah dimuat
            if (data.is_last_page || data.items.length < limit) {
              btnLoadMore.style.display = "none";
            } else {
              btnLoadMore.disabled = false;
              btnLoadMore.textContent = "Load More";
            }
          } else {
            btnLoadMore.style.display = "none";
          }
        })
        .catch((err) => {
          console.error("Error loading gallery:", err);
          btnLoadMore.textContent = "Gagal Memuat";
          btnLoadMore.disabled = false;
        });
    }

    btnLoadMore.addEventListener("click", loadMoreGallery);
  }
});
