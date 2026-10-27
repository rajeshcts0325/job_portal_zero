
document.addEventListener('DOMContentLoaded', () => {
    console.log('Laravel JavaScript is working!');

    document.querySelectorAll('[data-test-button]').forEach((button) => {
        button.addEventListener('click', () => {
            alert('JavaScript is working!');
        });
    });
});
