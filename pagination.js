document.addEventListener("DOMContentLoaded", (event) => {
    function findAndSetLinks(elmToSearch) {
        elmToSearch.querySelectorAll("a").forEach((link) => {
            link.addEventListener("click", function(e) {
                e.preventDefault();
                const pagination = link.closest(".inline-pagination-posts");
                const type = pagination.dataset.postType;
                const NewPage = new URL(link.href).searchParams.get(`${type}-page`);
                const params = new URLSearchParams(window.location.search);
                params.set(`${type}-page`, NewPage);
                history.pushState({}, "", window.location.pathname + "?" + params);
                requestDataAndSetHTML(pagination, type, NewPage)
            })
        })
    } 
    
    /** 
    * 
    * @param pagination The HTML elm with the class ".inline-pagination-posts" 
    * @param type What post type to fetch 
    * @param NewPage What page to fetch 
    **/
    function requestDataAndSetHTML(pagination, type, NewPage) {
        const module = pagination.closest(".et_pb_code_inner");
        if (module) {
            const request = new URLSearchParams({
                "page": pagination.dataset.page,
                "posts-per-page": pagination.dataset.postsPerPage,
                "pagination": "1",
                "post-type": type,
                [`${type}-page`]: NewPage,
                "path": window.location.pathname,
            });
            module.classList.add("loading");
            module.scrollIntoView({
                behavior: "smooth",
                block: "start"
            });
            fetch(`/wp-json/foun/v1/posts?` + request).then((response) => response.json()).then((data) => {
                module.innerHTML = data.html;
                findAndSetLinks(module);
                module.classList.remove("loading");
            })
        }
    }
    
    document.querySelectorAll(".inline-pagination-posts").forEach((pagination) => {
        findAndSetLinks(pagination);
    }) 
    
    window.addEventListener("popstate", () => {
        document.querySelectorAll(".inline-pagination-posts").forEach((pagination) => {
            const type = pagination.dataset.postType;
            const pageNumber = new URLSearchParams(window.location.search).get(`${type}-page`) || "1";
            requestDataAndSetHTML(pagination, type, pageNumber);
        })
    })
});