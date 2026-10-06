const container = document.getElementById("internship-timeline");
const dataNode = document.getElementById("internship-data");

if (container && dataNode && window.vis) {
    const items = new window.vis.DataSet(JSON.parse(dataNode.textContent));
    new window.vis.Timeline(container, items, {
        stack: true,
        zoomMin: 1000 * 60 * 60 * 24 * 7,
        zoomMax: 1000 * 60 * 60 * 24 * 365 * 3,
        margin: { item: 12 },
        orientation: "top",
    });
}
