<div wire:key='sweet-alert-{{ time() }}' x-data="sweetAlert" data-alert='@json(session()->get('alert'))'></div>

<script>
    document.addEventListener('alpine:init', () => {

        Alpine.data('sweetAlert', () => ({
            alertSession: null,
            init() {
                this.alertSession = JSON.parse(this.$el.dataset.alert || "null");
                Livewire.on('alert', ({
                    status,
                    message,
                    subtitle
                }) => {
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
                            htmlContainer: "!text-xs !my-0",
                        },
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        },
                    });
                    Toast.fire({
                        icon: status,
                        title: message ?? "",
                        text: subtitle ?? null,
                    });
                });

                Livewire.on('alert-confirm', ({
                    method,
                    id,
                    text
                }) => {
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

                if (this.alertSession) {
                    document.addEventListener('livewire:navigated', () => {
                        Swal.fire({
                            title: this.alertSession.title,
                            text: this.alertSession.message,
                            icon: this.alertSession.icon,
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    },{once:true})
                }
            }
        }))

    })
</script>


