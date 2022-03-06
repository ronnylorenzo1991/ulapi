import Swal from 'sweetalert2/dist/sweetalert2.js'

import 'sweetalert2/src/sweetalert2.scss'

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
export default {
    error,
    success,
}
