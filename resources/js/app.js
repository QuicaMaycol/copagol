import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import feather from 'feather-icons';
import Cropper from 'cropperjs';

window.Alpine = Alpine;
window.Cropper = Cropper; // Make Cropper globally available

Alpine.plugin(collapse);
Alpine.start();

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOMContentLoaded fired.');
    console.log('Type of feather:', typeof feather);
    console.log('Feather object:', feather);

    // Explicitly re-initialize Alpine.js for the entire body to ensure all components are processed
    Alpine.initTree(document.body);

    // Custom Feather Icons replacement logic to bypass DOMParser issue
    const featherIcons = document.querySelectorAll('[data-feather]');

    featherIcons.forEach(function(element) {
        const iconName = element.getAttribute('data-feather');
        if (feather.icons[iconName]) {
            // Get attributes from the original element to pass to the SVG
            const attrs = {};
            Array.from(element.attributes).forEach(attr => {
                if (attr.name !== 'data-feather') {
                    attrs[attr.name] = attr.value;
                }
            });

            // Generate SVG string
            const svgString = feather.icons[iconName].toSvg(attrs);

            // Create a temporary div to parse the SVG string into a DOM element
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = svgString;
            const svgElement = tempDiv.querySelector('svg');

            if (svgElement) {
                // Replace the original element with the new SVG element
                element.parentNode.replaceChild(svgElement, element);
            } else {
                console.error('Failed to parse SVG string for icon:', iconName, svgString);
            }
        } else {
            console.warn('Feather icon not found:', iconName);
        }
    });
});