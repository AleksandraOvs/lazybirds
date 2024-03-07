/**
 * Форма поиска
 * @type {HTMLElement}
 */
let header = document.getElementById('header'),
    headerSearchButton = document.getElementById('headerSearchButton'),
    headerSearchForm = document.getElementById('searchform'),
    headerSearchIcon = document.getElementById('headerSearchIcon'),
    headerSearchIconClose = document.getElementById('headerSearchIconClose');

headerSearchButton.addEventListener('click', function() {
    headerSearchForm.classList.toggle('active');

    if (headerSearchForm.classList.contains('active')) {
        header.style.boxShadow = 'none';
        headerSearchIcon.classList.remove('active');
        headerSearchIconClose.classList.add('active');
    } else {
        header.style.boxShadow = '0px 13px 13px rgba(0, 0, 0, 0.12)';
        headerSearchIcon.classList.add('active');
        headerSearchIconClose.classList.remove('active');
    }

    this.classList.toggle('active');

    let searchClear = headerSearchForm.querySelector('.header__search-clear'),
        searchInput = headerSearchForm.querySelector('.header__search-input');

    setTimeout(function(){
        if (headerSearchForm.classList.contains('active')) {
            searchInput.focus();
        }
    }, 200);

    searchClear.addEventListener('click', function() {
      searchInput.value = '';
      searchClear.style.display = 'none';
    });

    searchInput.addEventListener('input', function() {
      searchClear.style.display = 'block';
    });

  if (searchInput.value !== '') {
    document.addEventListener('keydown', function(e) {
      if (e.code === 'Enter') {
        headerSearchForm.submit();
      }
    })
  } else {
    searchClear.style.display = 'none';
  }
});


/**
 * Навигация
 * @type {HTMLElement}
 */
let headerMobileMenu = document.getElementById('headerMobileMenu'),
    headerMobileMenuButton = document.getElementById('headerMobileMenuButton'),
    headerMobileMenuOpen = document.getElementById('headerMobileMenuOpen'),
    headerMobileMenuClose = document.getElementById('headerMobileMenuClose'),
    headerSectionSocialContent = document.getElementById('headerSectionSocial').innerHTML,
    headerMobileMenuItems = document.getElementById('menu-verhnee-menyu-2020');


/**
 * Mobile social icons
 * Копируем разметку из десктопной версии в новый элемент мобильного меню
 * @type {HTMLDivElement}
 */
let headerMobileSocial = document.createElement('div');

headerMobileSocial.className = 'header__section_social-mobile';
headerMobileSocial.innerHTML = headerSectionSocialContent;

headerMobileMenu.querySelector('.header__mobile-menu-bottom').appendChild(headerMobileSocial);


/**
 * Mobile sub-menu
 * Работает, если в меню есть подпункты
 * @type {HTMLButtonElement}
 */
let headerMobileSubmenuButton = document.createElement('button'),
    headerMobileSubmenuButtonIcon = '<svg class="header__mobile-submenu-button_icon"><use xlink:href="/wp-content/themes/medialeaks_2k19/assets/build/sprite.svg#arrow-down"></use></svg>',
    headerMobileSubmenuItems = headerMobileMenuItems.querySelectorAll('.sub-menu');

headerMobileSubmenuButton.className = 'header__mobile-submenu-button';
headerMobileSubmenuButton.innerHTML = headerMobileSubmenuButtonIcon;

if (headerMobileSubmenuItems.length >= 1) { // Проверка на существование подпунктов меню
    for (let i = 0; i < headerMobileSubmenuItems.length; i++) {
        headerMobileSubmenuItems[i].parentElement.appendChild(headerMobileSubmenuButton);
    }
}


/**
 * Открытие/скрытие мобильного меню
 */
headerMobileMenuButton.addEventListener('click', function() {
    headerMobileMenu.classList.toggle('active');

    if (headerMobileMenu.classList.contains('active')) {
        headerMobileMenuClose.classList.add('active');
        headerMobileMenuOpen.classList.remove('active');
    } else {
        headerMobileMenuClose.classList.remove('active');
        headerMobileMenuOpen.classList.add('active');
    }
});


/**
 * Открытие/скрытие sub-menu
 * Конструкция такая потому что мы не изменяем стандартную разметку навигации wordpress
 * @type {NodeListOf<Element>}
 */
let headerMobileSubmenuButtons = document.querySelectorAll('.header__mobile-submenu-button');

for (let i = 0; i < headerMobileSubmenuButtons.length; i++) {
    let headerMobileSubmenuItem = headerMobileSubmenuButtons[i].parentElement.querySelector('.sub-menu'), // Родной элемент sub-menu от Wordpress
        headerMobileSubmenuItemContent = headerMobileSubmenuItem.innerHTML;

    headerMobileSubmenuButtons[i].parentElement.removeChild(headerMobileSubmenuItem);

    let headerMobileSubmenuItemContainer = document.createElement('ul');

    headerMobileSubmenuItemContainer.className = 'header__mobile-submenu-container';
    headerMobileSubmenuItemContainer.innerHTML = headerMobileSubmenuItemContent;

    headerMobileSubmenuButtons[i].parentElement.appendChild(headerMobileSubmenuItemContainer);

    headerMobileSubmenuButtons[i].addEventListener('click', function() {
        this.classList.toggle('active');

        this.parentElement.querySelector('.header__mobile-submenu-container').classList.toggle('active');

        if (this.classList.contains('active')) {
            this.parentElement.style.paddingTop = '14px';
        } else {
            this.parentElement.style.paddingTop = '0';
        }
    });

}
