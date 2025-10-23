window.addEventListener("alert", ({ detail: { status, message, text } }) => {
    var status = status ?? "success";
    const Toast = Swal.mixin({
        toast: true,
        position: "top-end",
        showConfirmButton: false,
        iconColor: "white",
        timer: 1500,
        timerProgressBar: true,
        customClass: {
            title: "!font-extrabold",
            popup: "colored-toast",
            htmlContainer: "!text-sm !my-0",
        },
        didOpen: (toast) => {
            toast.onmouseenter = Swal.stopTimer;
            toast.onmouseleave = Swal.resumeTimer;
        },
    });
    Toast.fire({
        icon: status,
        title: message ?? "",
        text: text ?? null,
    });
});

window.addEventListener("alert-confirm", ({ detail: { text, id, method } }) => {
    Swal.fire({
        title: "Apakah anda yakin?",
        text: text ?? "",
        icon: "warning",
        showCancelButton: true,
        customClass: {
            confirmButton: "!bg-primary hover:bg-primaryemphasis",
            cancelButton: "!bg-red-600 hover:bg-red-700",
        },
        cancelButtonText: "Batal",
        confirmButtonText: "Yes",
    }).then((result) => {
        if (result.isConfirmed) {
            window.dispatchEvent(
                new CustomEvent(method, {
                    detail: {
                        id: id,
                    },
                })
            );
        }
    });
});
