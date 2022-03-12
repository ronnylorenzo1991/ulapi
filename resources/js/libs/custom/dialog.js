import Swal from 'sweetalert2/dist/sweetalert2.js'

import 'sweetalert2/src/sweetalert2.scss'
import get from "lodash/get";

export async function error(message, errors = null) {
    return Swal.fire(message, errors ? getErrors(errors) : 'Algo anda mal!', 'error')
}

export async function success(message) {
    return Swal.fire('Correcto', message ? message : 'Ejecución Satisfactoria', 'success')
}

function getErrors(errors) {
    let str = ''
    Object.keys(errors).forEach((item, key) => {
        str += errors[item] + "<br>"
    })

    return str
}

export function confirm(title, message, showCloseButton = false, options = {}) {
    const textProp = /<[^>]+>/.test(message) ? 'html' : 'text'
    return new Promise((resolve, reject) => {
        Swal.fire({
            title: title === null ? '' : title,
            [textProp]: message,
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showCloseButton,
            allowOutsideClick: false,
            animation: window.enabledAnimations,
            ...options
        }).then(response => {
            if (get(response, 'dismiss') === 'close') {
                resolve('close')
            }
            resolve(get(response, 'value') === true)
        }).catch(() => {
            resolve(false)
        })
    })
}

export default {
    error,
    success,
    confirm,
}
