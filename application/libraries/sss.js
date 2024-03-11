
// ----=- show problem --------
$(document).on('click', '.actProblem', function () {
    ist_Id = $(this).attr('data-id');
    var url = API_URL + "Ticket_control/show_problem";

    $.ajax({
        url: url,
        type: 'POST',
        data: { ist_Id: ist_Id },
        dataType: 'json',
        success: function (response) {
            console.log(response);

            $('#SelProblem').val('');
            $('#mdetailprdlm').val('');
            $('.customCheckpb').prop('checked', false);

            var selectedValue = response.data[0].mjt_id; // ค่าที่ต้องการส่งไปยัง ProbConDropdown
            ProbConDropdown(selectedValue, function() {
                // ทำงานที่ต้องการหลังจาก ProbConDropdown เสร็จสิ้น
                response.data_check.forEach(function (problem) {
                    $('#customCheckpb' + problem.mpc_id).prop('checked', true);
                });

                $('#SelProblem').val(response.data[0].mpc_id);
                $('#mdetailprdlm').val(response.data[0].ipc_detail);

                var data_image = response.data_image[0];
                var maxFilesAllowed = 3;
                var e = `<div class="dz-preview dz-success dz-processing dz-image-preview dz-complete">
                            <div class="dz-details">
                                <div class="dz-thumbnail">
                                    <img data-dz-thumbnail>
                                    <span class="dz-nopreview">No preview</span>
                                    <div class="dz-success-mark"></div>
                                    <div class="dz-error-mark"></div>
                                    <div class="dz-error-message"><span data-dz-errormessage></span></div>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
                                    </div>
                                </div>
                                <div class="dz-filename" data-dz-name></div>
                                <div class="dz-size" data-dz-size></div>
                            </div>
                        </div>`;
                Dropzone.autoDiscover = false;
                var myDropzone = new Dropzone("#myDropzone", {
                    previewTemplate: e,
                    url: '/upload',
                    acceptedFiles: 'image/*',
                    maxFiles: maxFilesAllowed,
                    init: function () {
                        this.on("addedfile", function () {
                            if (this.files.length > this.options.maxFiles) {
                                this.removeFile(this.files[0]);
                            }
                        });
                    },
                    addRemoveLinks: true,
                    dictDefaultMessage: 'Drop your image here or click to upload',
                    parallelUploads: 1,
                    autoProcessQueue: false // ปิดการอัพโหลดอัตโนมัติ
                });

                var filesCount = 0;
                for (let i = 1; i <= 3; i++) {
                    var imageName = data_image['ipc_pic_' + i];
                    if (imageName != '') {
                        var imagePath = base_url('/assets/img/upload/problem/' + imageName);
                        let mockFile = { name: `${imageName}`, size: 12345 };
                        myDropzone.emit("addedfile", mockFile);
                        myDropzone.emit("thumbnail", mockFile, imagePath);
                        myDropzone.emit("complete", mockFile); // เพิ่มบรรทัดนี้เพื่อให้ Dropzone เป็นสถานะเต็มรูปแบบของไฟล์ที่ถูกเพิ่ม
                        filesCount++;
                    }
                }
            });
        },
        error: function (error) {
            console.error('Error fetching data from the API:', error);
        }
    });
});