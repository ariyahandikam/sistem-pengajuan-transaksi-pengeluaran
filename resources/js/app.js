import * as bootstrap from 'bootstrap';
import 'bootstrap-icons/font/bootstrap-icons.css';
import Chart from 'chart.js/auto';
import '../css/app.css';

window.bootstrap = bootstrap;
window.Chart = Chart;

// Initialize tooltips and popovers globally
document.addEventListener('DOMContentLoaded', () => {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Sidebar Toggle Logic
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebarMenu');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', (e) => {
            e.preventDefault();
            sidebar.classList.toggle('show');
        });
    }

    // Global fade-in animations
    const fadeElements = document.querySelectorAll('.fade-in-up');
    fadeElements.forEach((el, index) => {
        setTimeout(() => el.classList.add('in'), 100 + (index * 50));
    });
});

// Format Currency
function formatRupiah(n){
    if (n === null || typeof n === 'undefined') return 'Rp 0';
    const s = parseInt(n, 10).toString();
    return 'Rp ' + s.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// Animation for Counting numbers up
function animateCount(el, target, duration){
    target = Number(target) || 0;
    const start = 0;
    const startTime = performance.now();
    function step(now){
        const elapsed = now - startTime;
        const t = Math.min(1, elapsed / duration);
        const value = Math.floor(t * (target - start) + start);
        if (el.id === 'grandTotal' || el.classList.contains('format-rupiah')) {
            el.textContent = formatRupiah(value);
        } else {
            el.textContent = value;
        }
        if (t < 1) requestAnimationFrame(step);
    }
    requestAnimationFrame(step);
}

// Finance Dashboard Animations
function initFinanceAnimations(){
    document.querySelectorAll('.summary-value[data-target]').forEach((el, i) => {
        const target = el.getAttribute('data-target');
        el.textContent = '0';
        setTimeout(() => animateCount(el, target, 1000), 200 + i * 100);
    });

    const grand = document.getElementById('grandTotal');
    if (grand) {
        const target = grand.getAttribute('data-target');
        setTimeout(() => animateCount(grand, target, 1200), 500);
    }

    document.querySelectorAll('.progress-bar[data-percent]').forEach((bar, idx) => {
        const p = Number(bar.getAttribute('data-percent')) || 0;
        bar.style.width = '0';
        bar.setAttribute('aria-valuenow', '0');
        setTimeout(() => {
            bar.style.transition = 'width 1000ms cubic-bezier(0.4, 0, 0.2, 1)';
            bar.style.width = p + '%';
            bar.setAttribute('aria-valuenow', String(p));
        }, 300 + idx * 80);
    });

    const cards = Array.from(document.querySelectorAll('.card'));
    cards.forEach(c => c.classList.add('fade-in-up'));
    cards.forEach((c, idx) => setTimeout(() => c.classList.add('in'), 150 + idx * 50));
}

// Finance Dashboard Charts
function initFinanceCharts(submissionChart, categoryExpense, donutColors, donutBorders){
    if (typeof Chart === 'undefined') {
        console.error('Chart.js belum ter-load');
        return;
    }

    // Common Chart Options
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748B';
    Chart.defaults.scale.grid.color = 'rgba(0, 0, 0, 0.05)';

    const submissionCanvas = document.getElementById('submissionChart');
    if (submissionCanvas) {
        const ctxSubmission = submissionCanvas.getContext('2d');
        
        // Gradient for line chart
        const gradient = ctxSubmission.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

        new Chart(ctxSubmission, {
            type: 'line',
            data: {
                labels: submissionChart.labels,
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: submissionChart.data,
                    borderColor: '#4F46E5', // Indigo 600
                    backgroundColor: gradient,
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4, // Smooth curve
                    pointBackgroundColor: '#FFFFFF',
                    pointBorderColor: '#4F46E5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                    tooltip: {
                        backgroundColor: '#1E1B4B',
                        padding: 12,
                        titleFont: { size: 13, weight: '600' },
                        bodyFont: { size: 14, weight: '500' },
                        displayColors: false,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    const categoryCanvas = document.getElementById('categoryChart');
    if (categoryCanvas) {
        const ctxCategory = categoryCanvas.getContext('2d');
        const labels = categoryExpense.labels;

        const categoryDataLabelPlugin = {
            id: 'categoryDataLabelPlugin',
            afterDatasetsDraw(chart) {
                const ctx = chart.ctx;
                const dataset = chart.data.datasets[0];
                const meta = chart.getDatasetMeta(0);

                meta.data.forEach((arc, index) => {
                    const value = dataset.data[index];
                    if (!value || value === 0) return;

                    const center = arc.tooltipPosition();
                    const fontSize = Math.max(10, Math.min(14, arc.outerRadius / 6));
                    const text = value + '%';

                    ctx.save();
                    ctx.font = `bold ${fontSize}px 'Inter', sans-serif`;
                    ctx.textAlign = 'center';
                    ctx.textBaseline = 'middle';
                    ctx.fillStyle = '#ffffff';
                    
                    // Add subtle text shadow for better readability
                    ctx.shadowColor = 'rgba(0,0,0,0.5)';
                    ctx.shadowBlur = 4;
                    ctx.fillText(text, center.x, center.y);
                    ctx.restore();
                });
            }
        };

        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: categoryExpense.data,
                    backgroundColor: donutColors,
                    borderColor: '#ffffff',
                    borderWidth: 3,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1E1B4B',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return ` ${context.label}: ${context.parsed}%`;
                            }
                        }
                    }
                }
            },
            plugins: [categoryDataLabelPlugin]
        });
    }
}

// Export for global use
window.initFinanceAnimations = initFinanceAnimations;
window.initFinanceCharts = initFinanceCharts;
window.formatRupiah = formatRupiah;
window.animateCount = animateCount;
