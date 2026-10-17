let url = "https://admission-portal.in";
let key = "b3c30340-9a2f-11ed-969b-e2f72e00754c";

function checkLeadMobile(mobile) {
  if (mobile.length == 10) {
    const settings = {
      url: url + "/api/leads/check?mobile",
      method: "POST",
      timeout: 0,
      headers: {
        "Content-Type": "application/json",
      },
      dataType: "json",
      data: JSON.stringify({
        key: key,
        mobile: mobile,
      }),
    };

    $.ajax(settings).done(function (response) {
      if (response.status) {
        $("#f99e084d-afc3-4401-a8f6-b17a982d3381").html(response.message);
      } else {
        $("#f99e084d-afc3-4401-a8f6-b17a982d3381").html("");
      }
    });
  } else {
    $("#f99e084d-afc3-4401-a8f6-b17a982d3381").html("");
  }
}

function checkLeadEmail(email) {
  if (email.match("@")) {
    const settings = {
      url: url + "/api/leads/check?email",
      method: "POST",
      timeout: 0,
      headers: {
        "Content-Type": "application/json",
      },
      dataType: "json",
      data: JSON.stringify({
        key: key,
        email: email,
      }),
    };

    $.ajax(settings).done(function (response) {
      if (response.status) {
        $("#7c48f9fe-05cf-4b94-a418-634b01fac705").html(response.message);
      } else {
        $("#7c48f9fe-05cf-4b94-a418-634b01fac705").html("");
      }
    });
  } else {
    $("#7c48f9fe-05cf-4b94-a418-634b01fac705").html("");
  }
}

function getLeadCourses(id) {
  const settings = {
    url: url + "/api/settings/courses",
    method: "POST",
    timeout: 0,
    headers: {
      "Content-Type": "application/json",
    },
    dataType: "json",
    data: JSON.stringify({
      key: key,
    }),
  };

  $.ajax(settings).done(function (response) {
    if (response.status) {
      Object.keys(response.options).forEach(function (k) {
        $("#" + id).append(
          '<option value="' + k + '">' + response.options[k] + "</option>"
        );
      });
    }
  });
}

getLeadCourses("lead_courses");

function getLeadSubCourses(id, course_id) {
  $("#" + id).html("");
  const settings = {
    url: url + "/api/settings/sub-courses",
    method: "POST",
    timeout: 0,
    headers: {
      "Content-Type": "application/json",
    },
    dataType: "json",
    data: JSON.stringify({
      key: key,
      course: course_id,
    }),
  };

  $.ajax(settings).done(function (response) {
    if (response.status) {
      Object.keys(response.options).forEach(function (k) {
        $("#" + id).append(
          '<option value="' + k + '">' + response.options[k] + "</option>"
        );
      });
    }
  });
}

$("#pop_form").on("submit", function (e) {
  e.preventDefault();
  const formData = new FormData(this);
  formData.append("key", key);
  formData.append("source", "Website");
  formData.append("sub_source", "Form");
  const object = {};
  formData.forEach((value, key) => (object[key] = value));
  const json = JSON.stringify(object);
  $.ajax({
    url: url + "/api/leads/create",
    method: "POST",
    timeout: 0,
    data: json,
    dataType: "json",
    success: function (response) {
      if (response.status) {
        $("#pop_form")[0].reset();
        let timerInterval
        Swal.fire({
          icon: 'success',
          title: 'Thank You!',
          text: 'Please wait. You will be redirected to the application form!',
          timer: 3000,
          timerProgressBar: true,
          allowEscapeKey: false,
          allowOutsideClick: false,
          didOpen: () => {

          },
          willClose: () => {
            clearInterval(timerInterval)
          }
        }).then((result) => {
          /* Read more about handling dismissals below */
          if (result.dismiss === Swal.DismissReason.timer) {
            window.location.href = url +
              "?username=" +
              response.student_id +
              "&password=" +
              object["mobile"]
          }
        })
      } else {
        Swal.fire(data.message, "", "info");
      }
    },
  });
});