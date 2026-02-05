// Admin Dashboard JS - Migration from external project
document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu toggle logic
    const menuBtn = document.getElementById('mobile-menu-btn');
    const closeBtn = document.getElementById('mobile-close-btn');
    const overlay = document.getElementById('mobile-overlay');
    const body = document.body;

    const toggleMenu = () => {
        body.classList.toggle('menu-open');
    };

    if (menuBtn) menuBtn.addEventListener('click', toggleMenu);
    if (closeBtn) closeBtn.addEventListener('click', toggleMenu);
    if (overlay) overlay.addEventListener('click', toggleMenu);

    // Count Up Animation
    const counters = document.querySelectorAll('.stat-value');
    counters.forEach(counter => {
        const target = parseFloat(counter.getAttribute('data-target'));
        const prefix = counter.getAttribute('data-prefix') || '';
        const duration = 2000;
        const stepTime = 20;
        const steps = duration / stepTime;
        const increment = target / steps;

        let current = 0;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.innerText = prefix + Math.floor(current).toLocaleString();
        }, stepTime);
    });

    // Charts Initialization
    if (typeof ApexCharts !== 'undefined') {
        initCharts();
    }
});

function initCharts() {
    const revenueChartEl = document.querySelector("#revenueChart");
    if (revenueChartEl) {
        const options = {
            series: [{ name: 'Revenue', data: [23, 11, 22, 27, 13, 22, 37, 21, 44, 22, 30] }],
            chart: { height: 300, type: 'area', toolbar: { show: false } },
            colors: ['#3C50E0'],
            stroke: { curve: 'smooth', width: 2 },
            xaxis: { categories: ['Sep', 'Oct', 'Nov', 'Dec', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'] }
        };
        new ApexCharts(revenueChartEl, options).render();
    }

    const profitChartEl = document.querySelector("#profitChart");
    if (profitChartEl) {
        const options = {
            series: [{ name: 'Profit', data: [44, 55, 57, 56, 61, 58, 63] }],
            chart: { type: 'bar', height: 300, toolbar: { show: false } },
            plotOptions: { bar: { columnWidth: '40%', borderRadius: 4 } },
            colors: ['#3C50E0'],
            xaxis: { categories: ['M', 'T', 'W', 'T', 'F', 'S', 'S'] }
        };
        new ApexCharts(profitChartEl, options).render();
    }
}
