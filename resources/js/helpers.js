export const validationErrors = (errors) => {
    return Object.entries(errors).map(item => item[1]).join(', ')
}
