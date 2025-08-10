// mobile menu

function toggleMenu() {
  document.getElementById("navMenu").classList.toggle("show");
}
const menulinks = document.querySelectorAll(".menulink");
menulinks.forEach((item) => {
  item.addEventListener("click", () => {
    toggleMenu();
  });
});

// dynamic age

function getExactAge(birthDateString) {
  const birthDate = new Date(birthDateString);
  const now = new Date();

  let years = now.getFullYear() - birthDate.getFullYear();
  let months = now.getMonth() - birthDate.getMonth();
  let days = now.getDate() - birthDate.getDate();

  if (days < 0) {
    months -= 1;
    days += new Date(now.getFullYear(), now.getMonth(), 0).getDate();
  }

  if (months < 0) {
    years -= 1;
    months += 12;
  }

  return { years, months, days };
}

window.addEventListener("load", () => {
  const age = getExactAge("2000-06-20"); // تاریخ تولد
  const ageText = `${age.years} سال، ${age.months} ماه و ${age.days} روز`;
  document.getElementById("age").textContent = ageText;
});
function getExactAge(birthDateString) {
  const birthDate = new Date(birthDateString);
  const now = new Date();

  let years = now.getFullYear() - birthDate.getFullYear();
  let months = now.getMonth() - birthDate.getMonth();
  let days = now.getDate() - birthDate.getDate();

  if (days < 0) {
    months -= 1;
    days += new Date(now.getFullYear(), now.getMonth(), 0).getDate();
  }

  if (months < 0) {
    years -= 1;
    months += 12;
  }

  return { years, months, days };
}

window.addEventListener("load", () => {
  const age = getExactAge("2000-06-20"); // تاریخ تولد
  const ageText = `${age.years} ساڵ، ${age.months} مانگ و ${age.days} ڕۆژ`;
  document.getElementById("age1").textContent = ageText;
});
function getExactAge(birthDateString) {
  const birthDate = new Date(birthDateString);
  const now = new Date();

  let years = now.getFullYear() - birthDate.getFullYear();
  let months = now.getMonth() - birthDate.getMonth();
  let days = now.getDate() - birthDate.getDate();

  if (days < 0) {
    months -= 1;
    days += new Date(now.getFullYear(), now.getMonth(), 0).getDate();
  }

  if (months < 0) {
    years -= 1;
    months += 12;
  }

  return { years, months, days };
}

window.addEventListener("load", () => {
  const age = getExactAge("2000-06-20"); // تاریخ تولد
  const ageText = `${age.years} year ${age.months} month ${age.days} day` ;
  document.getElementById("age2").textContent = ageText;
});

// slider

const testimonials = document.querySelectorAll(".testimonials");
const nextBtnte = document.querySelector(".next-btn-te");
const prevBtnte = document.querySelector(".prev-btn-te");
const sliderContainer = document.querySelector(".slider-container");

let maxPos = testimonials.length;
function updateClasses(isNext) {
  testimonials.forEach((box) => {
    const currentPos = [...box.classList].find((cls) => cls.startsWith("pos"));
    const numberPos = parseInt(currentPos.split("_")[1]);
    box.classList.remove(currentPos);

    let newPos;
    if (isNext) {
      newPos = numberPos + 1;
    } else {
      newPos = numberPos - 1;
    }
    if (newPos > maxPos) {
      newPos = 1;
    } else if (newPos < 1) {
      newPos = maxPos;
    }

    box.classList.add(`pos_${newPos}`);
  });
}

nextBtnte.addEventListener("click", () => {
  updateClasses(true);
});
prevBtnte.addEventListener("click", () => {
  updateClasses(false);
});

let touchXstart = 0;
let touchXend = 0;

function touchUpdate() {
  if (touchXstart > touchXend && touchXstart - touchXend > 10) {
    updateClasses(false);
  } else if (touchXstart < touchXend && touchXend - touchXstart > 10) {
    updateClasses(true);
  }
}

sliderContainer.addEventListener("mousedown", (e) => {
  touchXstart = e.x;
});
sliderContainer.addEventListener("mouseup", (e) => {
  touchXend = e.x;
  touchUpdate();
});

sliderContainer.addEventListener("touchstart", (e) => {
  touchXstart = e.changedTouches[0].screenX;
});
sliderContainer.addEventListener("touchend", (e) => {
  touchXend = e.changedTouches[0].screenX;
  touchUpdate();
});

