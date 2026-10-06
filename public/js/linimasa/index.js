const container = document.getElementById("timeline");
const dataNode = document.getElementById("timeline-data");

if (container && dataNode && window.vis) {
    const records = JSON.parse(dataNode.textContent);
    const items = new window.vis.DataSet(records);
    new window.vis.Timeline(container, items, {
        stack: true,
        zoomMin: 1000 * 60 * 60 * 24 * 7,
        zoomMax: 1000 * 60 * 60 * 24 * 365 * 3,
        margin: { item: 12 },
        orientation: "top",
    });
}
