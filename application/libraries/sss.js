$(document).on('click', '#btnSearch', function () {
    var inpStartDate = $('#inpStartDate').val();
    var inpEndDate = $('#inpEndDate').val();
    var apiUrl = 'http://127.0.0.1/api/Remaining_Approval/show_data_list';
    $.ajax({
        url: apiUrl,
        type: 'POST',
        data: {
            inpStartDate: inpStartDate,
            inpEndDate: inpEndDate
        },
        dataType: 'json',
        success: function (data) {

            var html = "";
            // Loop through the data and append menu items
            for (var i = 0; i < data.length; i++) {

                html += `
                <tr>
                <td class="text-center">${i + 1}</td>
                <td class="text-center">${data[i].ist_line_cd == '' ? ${data[i].ist_area_other} : `${data[i].ist_line_cd}`}</td>
                <td class="text-center">${data[i].mts_name === null ? '-' : data[i].mts_name}</td>
                <td class="text-center">${data[i].ist_process === null ? '-' : data[i].ist_process}</td>
                <td class="text-center">
                    <div class="text-center">${data[i].mjt_name_thai === null ? '-' : data[i].mjt_name_thai}</div>
                    <div class="text-center">${data[i].mjt_name_eng === null ? '-' : data[i].mjt_name_eng}</div>
                </td>
                <td class="text-center">${data[i].ist_date === null ? '-' : data[i].ist_date}</td>
                <td class="text-center">
                    <div class="text-center"><small class="emp_post text-truncate text-muted">${data[i].ist_request_by}</small></div>
                    <div class="text-center">${data[i].ist_type == 1 ? 'APPLICATION' : 'WEBSITE'}</div></td>
                <td class="text-center">${data[i].swa_emp_code === null ? '-' : data[i].swa_emp_code.split(",").join(" | ")}</td>
                <td class="text-center"><span class="badge bg-label-${data[i].ist_status_flg == 7 ? 'info' : 'Unknown'}"> ${data[i].ist_status_flg == 7 ? 'Wait Approval' : 'Unknown'}</span> </td>
                <td class="text-center">
                    <button class="btn btn-secondary">Detail</button>
                    <button class="btn btn-primary" id="btnApprove"  me-1" id="flgStatus" data-sa-id="${data[i].ist_id}">Confirm</button>
                    <button  class="btnDeny btn-danger btn" data-bs-toggle="modal" data-bs-target="#mdlDeny" me-1" id="flgStatus" data-dy-id="${data[i].ist_id}">Deny</button>
                </td>
            </tr>
            `;

                // เรียกใช้งาน generateAvatarHTML เพื่อสร้าง HTML สำหรับ avatar
                var avatarHtml = generateAvatarHTML(data, i);
                $(`#avatarGroup_${i}`).html(avatarHtml);
            }

            // ทำลาย DataTable ที่มีอยู่ก่อนที่จะปรับปรุงเนื้อหาของตาราง
            $('#tblRemainingApproval').DataTable().destroy();

            // ปรับปรุงเนื้อหาของตารางและเริ่มใช้ DataTable ใหม่
            $("#tbody").html(html).promise().done(() => {
                $("#tblRemainingApproval").DataTable({ scrollX: true });
            });
        },
        error: function (xhr, status, error) {
            console.error('Error:', error);
        }
    });
});