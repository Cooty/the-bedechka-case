export const getCases = () => {
    return fetch(window._config.mapCaseApiUrl)
        .then(data => data.json())
}
