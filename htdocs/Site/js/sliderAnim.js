
// Переменные
const slider = document.querySelector('.slider');
const sliderImages = document.querySelectorAll('.slider_image');
const sliderLine = document.querySelector('.slider_line');

const sliderBtnNext = document.querySelector('.slider_btn_next');
const sliderBtnPrev = document.querySelector('.slider_btn_prev');
        
let sliderCount = 0;
let sliderWidth = slider.offsetWidth;

// Автоматическое перелистывание слайдов
// setInterval(() => {
//     nextSlide()
// }, 3000);

// Перемотка слайдера вперед по нажатию на кнопку NEXT
sliderBtnNext.addEventListener('click', nextSlide());

// Перемотка слайдера назад по нажатию на кнопку PREV
sliderBtnPrev.addEventListener('click', prevSlide());


// Функции
function nextSlide() {
    sliderCount++;
    
    if (sliderCount >= sliderImages.length) {
        sliderCount = 0;
    }
    rollSlider();
}

function prevSlide() {
    sliderCount--;
    
    if (sliderCount < 0) {
        sliderCount = sliderImages.length -1;
    }
    rollSlider();
}

function rollSlider() {
    sliderLine.style.transform = `translateX(${-sliderCount * sliderWidth}px)`;
}