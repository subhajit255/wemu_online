let imageId = [];
$(document).on('click', '.removeImage', function (e) {
    const currentImageId = $(this).attr('data-id');
    if(!imageId.includes(currentImageId)){
        imageId.push(currentImageId);
    }
    $('#remove_image').val(JSON.stringify(imageId));
});

function rMdiv(flag) {
    $(`#imgCls_${flag}`).remove();
}

function previewImages() {
    var preview = document.querySelector('#previewImages');
    preview.innerHTML = ''; // Clear previous images
    if (this.files) {
        [].forEach.call(this.files, readAndPreview);
    }

    function readAndPreview(file) {
        if (!/\.(jpe?g|png|gif|svg)$/i.test(file.name)) {
            Swal.fire({
                icon: 'error',
                title: `${file.name} is not an image`,
                showConfirmButton: false,
                timer: 1500
            });
            return;
        }
        var reader = new FileReader();
        reader.addEventListener("load", function () {
            var imageContainer = document.createElement('div');
            imageContainer.classList.add('image-container');

            var image = new Image();
            image.height = 100;
            image.title = file.name;
            image.src = this.result;

            var deleteIcon = document.createElement('i');
            deleteIcon.classList.add('fas', 'fa-trash', 'delete-image');
            deleteIcon.style.cursor = 'pointer';
            deleteIcon.addEventListener('click', function () {
            imageContainer.remove();
            });

            imageContainer.appendChild(image);
            imageContainer.appendChild(deleteIcon);
            preview.appendChild(imageContainer);
        });
        reader.readAsDataURL(file);
    }
}
document.querySelector('#file').addEventListener("change", previewImages);
