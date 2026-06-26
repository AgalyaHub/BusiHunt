/* ============================================================
   BUSI HUNT — main.js
   ============================================================ */

/* ---------- NAVBAR SCROLL ---------- */
var navbar = document.getElementById('navbar');
if (navbar) {
  window.addEventListener('scroll', function() {
    if (window.scrollY > 50) {
      navbar.classList.add('scrolled');
    } else {
      navbar.classList.remove('scrolled');
    }
  });
}

/* ---------- NAVBAR LOGIN/LOGOUT STATE ---------- */
function updateNavbar() {
  var user = null;
  try {
    user = JSON.parse(localStorage.getItem('bh_user'));
  } catch(e) {}

  var navActions = document.querySelector('.nav-actions');
  if (!navActions) return;

  if (user && user.first_name) {
    var adminBtn = '';
    if (user.role === 'admin') {
      adminBtn = '<a href="admin.php" class="btn btn-outline">⚙️ Dashboard</a>';
    }
    navActions.innerHTML =
      '<span style="color:white;font-size:0.9rem;font-weight:600;">👋 ' + user.first_name + '</span>' +
      adminBtn +
      '<a href="php/logout.php" class="btn btn-primary">Logout</a>';
  } else {
    navActions.innerHTML =
      '<a href="login.html" class="btn btn-outline">Sign In</a>' +
      '<a href="register.html" class="btn btn-primary">Join Now</a>';
  }
}

updateNavbar();

/* ---------- MOBILE MENU ---------- */
var hamburger  = document.querySelector('.hamburger');
var mobileMenu = document.querySelector('.mobile-menu');
var mobileClose = document.querySelector('.mobile-close');

if (hamburger && mobileMenu) {
  hamburger.addEventListener('click', function() {
    mobileMenu.classList.add('open');
    document.body.style.overflow = 'hidden';
  });
}

if (mobileClose && mobileMenu) {
  mobileClose.addEventListener('click', function() {
    mobileMenu.classList.remove('open');
    document.body.style.overflow = '';
  });
}

document.querySelectorAll('.mobile-menu a').forEach(function(link) {
  link.addEventListener('click', function() {
    if (mobileMenu) {
      mobileMenu.classList.remove('open');
      document.body.style.overflow = '';
    }
  });
});

/* ---------- FADE-IN ON SCROLL ---------- */
var fadeEls = document.querySelectorAll('.fade-in');

var observer = new IntersectionObserver(function(entries) {
  entries.forEach(function(entry) {
    if (entry.isIntersecting) {
      entry.target.classList.add('visible');
      observer.unobserve(entry.target);
    }
  });
}, { threshold: 0.12 });

fadeEls.forEach(function(el) { observer.observe(el); });

/* ---------- EVENT FILTER ---------- */
var filterBtns = document.querySelectorAll('.filter-btn');
var eventCards = document.querySelectorAll('.event-list-card');

filterBtns.forEach(function(btn) {
  btn.addEventListener('click', function() {
    filterBtns.forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    var filter = btn.dataset.filter;
    eventCards.forEach(function(card) {
      if (filter === 'all' || card.dataset.category === filter) {
        card.style.display = 'grid';
        setTimeout(function() { card.style.opacity = '1'; }, 10);
      } else {
        card.style.opacity = '0';
        setTimeout(function() { card.style.display = 'none'; }, 250);
      }
    });
  });
});

/* ---------- ACTIVE NAV LINK ---------- */
var currentPage = window.location.pathname.split('/').pop() || 'index.html';
document.querySelectorAll('.nav-links a').forEach(function(link) {
  if (link.getAttribute('href') === currentPage) {
    link.style.color = '#C9A84C';
    link.style.fontWeight = '700';
  }
});