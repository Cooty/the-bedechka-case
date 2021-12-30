const tagsToReplace = {
    "&": "&amp;",
    "<": "&lt;",
    ">": "&gt;",
    "\"": "\t&quot;"
};

function replaceTag(tag: string) {
    // @ts-ignore
    return tagsToReplace[tag] || tag;
}

export default function sanitizeHTML(str: string) {
    const regEx = /[<>&"]/g;

    return regEx.test(str) ? str.replace(regEx, replaceTag) : str;
}