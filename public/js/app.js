// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Parallax effect on scroll
let lastScrollY = window.scrollY;

window.addEventListener('scroll', () => {
    const scrollY = window.scrollY;

    // Floating shapes parallax
    const shapes = document.querySelectorAll('.floating-shape');
    shapes.forEach((shape, index) => {
        const speed = 0.5 + (index * 0.2);
        const yPos = -(scrollY * speed);
        shape.style.transform = `translateY(${yPos}px)`;
    });

    // Hero image parallax
    const heroImage = document.querySelector('.floating-image');
    if (heroImage) {
        const yPos = scrollY * 0.3;
        heroImage.style.transform = `translateY(${yPos}px)`;
    }

    lastScrollY = scrollY;
});

// Intersection Observer for fade-in animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe feature cards
document.querySelectorAll('.feature-card').forEach((card, index) => {
    card.style.opacity = '0';
    card.style.transform = 'translateY(30px)';
    card.style.transition = `all 0.6s ease ${index * 0.1}s`;
    observer.observe(card);
});

// Password strength indicator
const passwordInput = document.getElementById('password');
if (passwordInput) {
    passwordInput.addEventListener('input', (e) => {
        const password = e.target.value;
        const strengthBar = document.querySelector('.strength-bar');

        if (strengthBar) {
            let strength = 0;

            if (password.length >= 8) strength += 25;
            if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength += 25;
            if (password.match(/[0-9]/)) strength += 25;
            if (password.match(/[^a-zA-Z0-9]/)) strength += 25;

            strengthBar.style.width = `${strength}%`;

            if (strength <= 25) {
                strengthBar.style.background = 'linear-gradient(135deg, #f5576c 0%, #f093fb 100%)';
            } else if (strength <= 50) {
                strengthBar.style.background = 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)';
            } else if (strength <= 75) {
                strengthBar.style.background = 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)';
            } else {
                strengthBar.style.background = 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)';
            }
        }
    });
}

// Form validation animations
const forms = document.querySelectorAll('.auth-form');
forms.forEach(form => {
    form.addEventListener('submit', (e) => {
        e.preventDefault();

        // Add loading state to submit button
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.style.opacity = '0.7';
            submitBtn.style.pointerEvents = 'none';

            // Simulate form submission
            setTimeout(() => {
                submitBtn.style.opacity = '1';
                submitBtn.style.pointerEvents = 'auto';
            }, 2000);
        }
    });

    // Input focus animations
    const inputs = form.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function () {
            this.parentElement.classList.remove('focused');
        });
    });
});

// Animate stats on scroll
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const statNumbers = entry.target.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const target = stat.textContent;
                const number = parseInt(target.replace(/\D/g, ''));
                const suffix = target.replace(/[0-9]/g, '');

                let current = 0;
                const increment = number / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= number) {
                        stat.textContent = target;
                        clearInterval(timer);
                    } else {
                        stat.textContent = Math.floor(current) + suffix;
                    }
                }, 30);
            });
            statsObserver.unobserve(entry.target);
        }
    });
}, observerOptions);

const heroStats = document.querySelector('.hero-stats');
if (heroStats) {
    statsObserver.observe(heroStats);
}

// Navbar scroll effect
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
});

// Hover effect for opportunity tags
const opportunityTags = document.querySelectorAll('.opportunity-tag');
opportunityTags.forEach(tag => {
    tag.addEventListener('mouseenter', function () {
        this.style.transform = 'translateX(8px) scale(1.02)';
    });

    tag.addEventListener('mouseleave', function () {
        this.style.transform = 'translateX(0) scale(1)';
    });
});

// Add ripple effect to buttons
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function (e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');

        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

console.log('InnoLearn JavaScript loaded successfully! 🚀');
