const chartDefinitions = [
    {
        canvas: 'statusPieChart',
        dataKey: 'statusData',
        labelKey: 'status_pemakaian',
        colors: ['#28a745', '#dc2626'],
        sort: (items) => items.sort((a) => (a.status_pemakaian === 'Aktif' ? -1 : 1)),
    },
    {
        canvas: 'jenisPieChart',
        dataKey: 'jenisData',
        labelKey: 'jenis',
        colors: ['#2ecc71', '#3498db', '#e74c3c'],
    },
    {
        canvas: 'basisPieChart',
        dataKey: 'basisData',
        labelKey: 'basis_aplikasi',
        colors: ['#3B82F6', '#10B981', '#F59E0B', '#6366F1'],
    },
    {
        canvas: 'pengembangPieChart',
        dataKey: 'pengembangData',
        labelKey: 'pengembang',
        colors: ['#8B5CF6', '#14B8A6', '#F43F5E', '#0EA5E9', '#22C55E'],
    },
];

function chartOptions() {
    return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: window.innerWidth <= 1050 ? 'top' : 'left',
                labels: { boxWidth: 10, padding: 20, usePointStyle: true, pointStyle: 'circle' },
            },
            tooltip: {
                callbacks: {
                    label(context) {
                        const total = context.dataset.data.reduce(
                            (sum, value) => sum + Number(value),
                            0,
                        );
                        const percentage = total ? ((context.raw / total) * 100).toFixed(1) : '0.0';
                        return `${context.raw} (${percentage}%)`;
                    },
                },
            },
        },
        onResize(chart, size) {
            chart.options.plugins.legend.position = size.width <= 1050 ? 'top' : 'left';
        },
    };
}

function renderChart(definition, response) {
    const canvas = document.getElementById(definition.canvas);
    if (!canvas) return;

    const items = definition.sort
        ? definition.sort([...response[definition.dataKey]])
        : response[definition.dataKey];

    new window.Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: items.map((item) => `${item[definition.labelKey]} (${item.total})`),
            datasets: [
                {
                    data: items.map((item) => item.total),
                    backgroundColor: definition.colors,
                    borderWidth: 0,
                },
            ],
        },
        options: chartOptions(),
    });
}

async function loadChartData() {
    try {
        const response = await fetch('/chart-data', { headers: { Accept: 'application/json' } });
        if (!response.ok) throw new Error('Chart data could not be loaded.');
        const data = await response.json();
        chartDefinitions.forEach((definition) => renderChart(definition, data));
    } catch (error) {
        document.querySelectorAll('.chart-container').forEach((container) => {
            container.textContent = 'Data grafik tidak tersedia.';
        });
    }
}

document.addEventListener('DOMContentLoaded', loadChartData);
