const main_content_swiper = new Swiper('.swiper-container', {
  init: false,
  direction: 'vertical',
  updateOnWindowResize: true,
  mousewheel: false,
  pagination: {
    el: '.swiper-pagination',
    dynamicBullets: true,
  },
});
window.addEventListener('DOMContentLoaded', () => {
  // initialize main window sweiper
  setTimeout(()=>{
    main_content_swiper.init();
  }, 1000);
});


function generate_slide(num, element, html=null) {
  let next_slide = num + 1;
  let slide_html = (html !== null) ? html : ``;
  if (html === null) {
    switch (num) {
      case 1:
        // generate first slide
        slide_html = `
          <form class="row header" id="validateEmployee">
            <h2>Enter employee number or email address</h2>
            <input type="text" name="employee_data" placeholder="Employee public ID/number or Employee email" />
          </form>
          <div class="row btn_holder">
            <button type="button" id="gotoSlide-2-new"> <span>Register new employee</span> </button>
            <button type="button" id="gotoSlide-2"> <span>Next</span> </button>
          </div>
        `;
        break;

      case '2-new':
        slide_html = `
          <form class="row header" id="reg_employee">
            <h2 style="margin-bottom: 0px;">Register new employee</h2>
            <label>Firstname *</label>
            <input type="text" name="firstname" placeholder="Employee firstname" required />

            <label>Lastname *</label>
            <input type="text" name="lastname" placeholder="Employee lastname" required />

            <label>Email *</label>
            <input type="email" name="email" placeholder="Employee email" required />

            <label>PayRate ( £ / hour ) *</label>
            <input type="text" name="payrate" placeholder="Employee payrate" required />

            <label>Restaurant Postal Code *</label>
            <input type="text" name="restaurant_pc" placeholder="Restaurant postal code of where employee is assigned to" required />
          </form>
          <div class="row btn_holder">
            <button type="button" id="gotoSlide-1"> <span>Back</span> </button>
            <button type="button" id="gotoSlide-3"> <span>Register</span> </button>
          </div>
        `;
        break;

      case '3-new':
      slide_html = `
        <div class="row header">
          <h2>Do you still want to send a pay change request?</h2>
        </div>
        <div class="row btn_holder">
          <button type="button" id="gotoSlide-1-f-2"> <span>Yes</span> </button>
          <button type="button" id="gotoSlide-4"> <span>No</span> </button>
        </div>
      `;
        break;

      case 'final-slide':
        slide_html = `
          <div class="row header">
            <h1>Have a nice day!</h1>
            <h3>Any awaiting decision will be sent when signed by an area manager.</h3>
          </div>
          <div class="row btn_holder">
            <button type="button" id="services" onclick="window.location.href='./'"> <span>Services</span> </button>
          </div>
        `;
        break;

      default:
        break;
    }
  }

  let slide_container = document.createElement('DIV');
      slide_container.setAttribute('class', 'container main slide_'+num);
      slide_container.insertAdjacentHTML('beforeend', slide_html);
      clearDOM(element);
      element.append(slide_container);

  // SLIDE 1 ------------------------------------------------------------------>
  let slide_1_next_btn = document.getElementById('gotoSlide-2');
  let slide_1_new_btn = document.getElementById('gotoSlide-2-new');
  if (slide_1_next_btn && slide_1_new_btn) {
    // Validate employee profile
    slide_1_next_btn.addEventListener('click', (e) => {
      // Search database for employee ID
      let slide_2 = document.getElementById('slide-2');
      let formJson = srlToJson( $('#validateEmployee') );
      if (formJson.employee_data == '') {
        swal({
          icon: 'error',
          title: 'Empty field',
          text: 'You must enter an employee email or public ID to proceed, you can register a new employee without filling the field.',
          button: 'Okay'
        });
        return;
      }
      ajx({
        type: 'POST',
        url: './validate_employee',
        data: formJson,
        success: (res) => {
          if (isJson(res)) {
            res = JSON.parse(res);
            if (res.firstname === null) {
              swal({
                icon: 'warning',
                title: 'Invalid ID or Email',
                text: 'You must enter an employee email or public ID to proceed, you can register a new employee without filling the field.',
                button: 'Okay'
              });
              return;
            }
            let newHtmlTemplate = `
            <form class="row header" id="paychangeform">
              <h2>Hello ${res.firstname} ${res.lastname},</h2>
              <h3>Fill the pay change request form below.</h3>

              <input type="hidden" name="PubID" value="${res.public_id}" />

              <label>Current Pay Rate ( £ / hour )</label>
              <input type="text" name="old_pay_rate" value="${res.payrate}" disabled />

              <label>Change Pay Rate ( £ / hour )</label>
              <input type="text" name="new_pay_rate" placeholder="Enter new Pay Rate" value="${res.payrate}" />

              <label>Tell us why you need this pay change</label>
                <textarea name="pay_change_reason"></textarea>
            </form>
            <div class="row btn_holder">
              <button type="button" id="sendPayChange"> <span>Submit</span> </button>
            </div>
            `;
            generate_slide(null, slide_2, newHtmlTemplate);
            if (main_content_swiper) main_content_swiper.slideTo(2, 1000, false);

            let paychangesubmit = document.getElementById('sendPayChange');
            paychangesubmit.addEventListener('click', () => {
              ajx({
                type: 'POST',
                url: './employee_pay_change',
                data: srlToJson( $('#paychangeform') ),
                success: (res) => {
                  console.log(res);
                  if (isJson(res)) {
                    res = JSON.parse(res);
                    if (res.res) {
                      swal({
                        icon: 'success',
                        title: 'Sent',
                        text: 'An area manager will respond to your request in less than 10 working days.',
                        button: 'Okay'
                      }).then(()=>{
                        // Load final slide
                        if (document.getElementById('slide-3') === null) {
                          main_content_swiper.appendSlide(['<div class="swiper-slide" id="slide-3-final"></div>']);
                        } else {
                          clearDOM(document.getElementById('slide-3'));
                        }
                        // Generate slide 3 for the next step here
                        let slide_3_final = document.getElementById('slide-3-final');
                        main_content_swiper.slideTo(3, 1000, false);
                        main_content_swiper.allowSlidePrev = false;
                        generate_slide('final-slide', slide_3_final);
                      });
                    }
                  }
                },
                load: 'up'
              });
            });

          }
        },
        complete: () => {
          dlog.log('Validation form processed!');
        },
        load: 'up'
      });
    });

    // Create new employee profile
    slide_1_new_btn.addEventListener('click', (e) => {
      // Register new employee
      let slide_2 = document.getElementById('slide-2');
      let makeSlide1 = generate_slide('2-new', slide_2);
      if (main_content_swiper) main_content_swiper.slideTo(2, 1000, false);

      let goBackBtn = document.getElementById('gotoSlide-1');
      goBackBtn.addEventListener('click', (e) => {
        if (main_content_swiper) main_content_swiper.slideTo(1, 1000, false);
        clearDOM(slide_2);
      });

      let registerEmployee = document.getElementById('gotoSlide-3');
      registerEmployee.addEventListener('click', (e) => {
        let formJson = srlToJson( $('#reg_employee') );
        if (formJson.firstname == ''
            || formJson.lastname == ''
            || formJson.email == ''
            || formJson.payrate == ''
            || formJson.restaurant_pc == '') {
          swal({
            icon: 'error',
            title: 'Empty field',
            text: 'You must fill the required fields to proceed.',
            button: 'Okay'
          });
          return;
        }
        ajx({
          type: 'POST',
          url: './register_employee',
          data: srlToJson( $('#reg_employee') ),
          success: (res) => {
            if (isJson(res)) {
              res = JSON.parse(res);
              if (res.res == "invalid_postal_code") {
                swal({
                  icon: 'warning',
                  title: 'Restaurant not found!',
                  text: 'The restaurant postal code you entered is not associated with the Karali-Group company',
                  button: 'Try again'
                });
                return;
              }

              if (res.res === true) {
                swal({
                  icon: 'success',
                  title: 'Employee was registered successfully',
                  text: 'Employee\'s public ID/number is '+res.employee_id+', the welcome email has been sent to the employee!',
                  button: 'Great!',
                }).then(() => {
                  if (document.getElementById('slide-3') === null) {
                    main_content_swiper.appendSlide(['<div class="swiper-slide" id="slide-3"></div>']);
                  } else {
                    clearDOM(document.getElementById('slide-3'));
                  }
                  // Generate slide 3 for the next step here
                  let slide_3 = document.getElementById('slide-3');
                  generate_slide('3-new', slide_3);
                  main_content_swiper.slideTo(3, 1000, false);
                });
              } else {
                swal({
                  icon: 'error',
                  title: 'Internal Server Error',
                  text: 'Something unexpected has happened on the server.'
                });
              }
            }
          },
          complete: () => {
            dlog.log('Registeration form processed!');
          },
          errorMethod: () => {
            swal({
              icon: 'error',
              title: 'Internal Server Error',
              text: 'Something unexpected has happened on the server.'
            });
          },
          load: 'up'
        });
        // if (main_content_swiper) main_content_swiper.slideTo(1, 1000, false);
      });
    });
  }
  // -------------------------------------------------------------------------->


  // SLIDE 3 NEW -------------------------------------------------------------->
  if (document.getElementById('slide-3') !== null) {
    let slide_1_f_2_btn = document.getElementById('gotoSlide-1-f-2');
        slide_1_f_2_btn.addEventListener('click', (e) => {
          swal({
            icon: 'warn',
            title: 'Page refresh',
            text: 'The page will be refreshed!',
            button: 'Go ahead!'
          }).then(() => {
            window.location.href = './pay_change';
          });
        });

    let slide_4_btn = document.getElementById('gotoSlide-4');
        slide_4_btn.addEventListener('click', (e) => {
          // No
          if (document.getElementById('slide-4') === null) {
            main_content_swiper.appendSlide(['<div class="swiper-slide" id="slide-4"></div>']);
            let slide_4 = document.getElementById('slide-4');
            generate_slide('final-slide', slide_4);
            main_content_swiper.slideTo(4, 1000, false);
            main_content_swiper.allowSlidePrev = false;
          }
        });
  }
  // -------------------------------------------------------------------------->

}
