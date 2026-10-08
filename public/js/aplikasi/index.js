import { showFlashMessage } from '../common/http.js';
import { initializeApplicationActions } from './actions.js';
import { initializeApplicationForm } from './form.js';
import { initializeApplicationFilters } from './list.js';

const page = document.querySelector('#applicationPage');

if (page) {
    const form = initializeApplicationForm(page.dataset.baseUrl);
    initializeApplicationActions(page.dataset.baseUrl, form.openEdit);
    initializeApplicationFilters();
    showFlashMessage();
}
