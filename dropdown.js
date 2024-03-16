// Javascript for dropdown functionality

function ShowDropdown(dropdown) {
    var dd = document.getElementById(dropdown);
    if (!dd.classList.contains('show'))
        dd.classList.add('show');
}

function HideDropdown(dropdown) {
    var dd = document.getElementById(dropdown);
    if (dd.classList.contains('show'))
        dd.classList.remove('show');
}
