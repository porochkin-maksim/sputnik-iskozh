export function useBreadcrumbHtml () {
    const setBreadcrumbHtml = (html) => {
        const breadcrumb = document.querySelector('.breadcrumb');
        if (breadcrumb && html) {
            breadcrumb.innerHTML = html;
        }
    };

    return {
        setBreadcrumbHtml,
    };
}
