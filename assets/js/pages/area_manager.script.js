window.addEventListener('DOMContentLoaded', () => {
  // initialize main window sweiper
  (()=> {
    const request_row_template = (arr) => {
      let tr = document.createElement('TR');
      let datetime = arr.datetime;
      let approval = (arr.decision === null) ? "Pending" : ((arr.decision.approval == 1) ? "Approved" : "Declined");
      let html = `
        <td>RID-${arr.rid}</td>
        <td>Pay Change</td>
        <td>${arr.fname} ${arr.lname} - ${arr.restaurant_name}</td>
        <td>${approval}</td>
        <td>${datetime}</td>
      `;
      tr.insertAdjacentHTML('beforeend', html);
      return tr;
    };

    // Load all requests from server
    ajx({
      load: 'up',
      type: 'POST',
      url: './load_area_manager_inbox',
      data: {},
      success: (res) => {
        // console.log(res);
        if (isJson(res)) {
          res = JSON.parse(res);
          let rowHolder = document.getElementById('dynamic_row_holder');
          clearDOM(rowHolder);

          // Process each and every pay change request
          if (res == "") return;
          res.forEach((item, i) => {
            // console.log(item);
            let row = request_row_template(item);
            rowHolder.append(row);
            let decision = item.decision;
            if (decision === null) {
              row.addEventListener('click', (e) => {
                let domHTML = document.createElement('DIV');
                    domHTML.innerHTML = `
                      <div class="container">
                        <div class="row">
                          <div class="container">
                            <p>This pay change is pending approval</p>
                            <div class="row">
                              <div class="col swal-label">Name</div>
                              <div class="col swal-val">${item.fname} ${item.lname}</div>
                            </div>
                            <div class="row">
                              <div class="col swal-label">PayRate (Old)</div>
                              <div class="col swal-val">£${item.old_rate} per hour</div>
                            </div>
                            <div class="row">
                              <div class="col swal-label">PayRate (New)</div>
                              <div class="col swal-val">£${item.new_rate} per hour</div>
                            </div>
                            <div class="row">
                              <div class="col swal-label">Reason</div>
                              <div class="col swal-val">${item.reason}</div>
                            </div>
                            <br>
                            <h5>Restaurant</h5>
                            <div class="row">
                              <div class="col swal-label">Name</div>
                              <div class="col swal-val">${item.restaurant_name}</div>
                            </div>
                            <div class="row">
                              <div class="col swal-label">Post code</div>
                              <div class="col swal-val">${item.restaurant_postal_code}</div>
                            </div>
                            <div class="row">
                              <div class="col swal-label">City</div>
                              <div class="col swal-val">${item.restaurant_area_name}</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    `;
                swal({
                  title: `Request for a change in pay rate`,
                  content: domHTML,
                  buttons: {
                    cancel: "Close",
                    approve: {
                      text: "Approve",
                      value: "approve",
                    },
                    decline: true,
                  },
                }).then((result) => {
                  switch (result) {
                    case 'approve':
                    ajx({
                      load: 'up',
                      type: 'POST',
                      url: './pay_change_decision',
                      data: {
                        pid: item.rid,
                        approval: 1
                      },
                      success: (res) => {
                        console.log(res);
                        if (isJson(res)) {
                          res = JSON.parse(res);
                          if (res.res) {
                            swal({
                              icon: 'success',
                              title: 'Pay Change Approved',
                              text: `Pay change for ${item.fname} ${item.lname} has been approved and signed by you!`
                            }).then(() => {
                              row.style.display = "none";
                            });
                          } else {
                            swal({
                              icon: 'error',
                              title: 'Uh Oh!',
                              text: `Encoutered an error while approving the pay change request, try again in a few seconds.`
                            });
                          }
                        }
                      }
                    });
                      break;

                    case 'decline':
                    ajx({
                      load: 'up',
                      type: 'POST',
                      url: './pay_change_decision',
                      data: {
                        pid: item.rid,
                        approval: 0
                      },
                      success: (res) => {
                        if (isJson(res)) {
                          res = JSON.parse(res);
                          if (res.res) {
                            swal({
                              icon: 'success',
                              title: 'Pay Change Unapproved',
                              text: `Pay change for ${item.fname} ${item.lname} has been unpproved and signed by you!`
                            }).then(() => {
                              row.style.display = "none";
                            });
                          } else {
                            swal({
                              icon: 'error',
                              title: 'Uh Oh!',
                              text: `Encoutered an error while declining the pay change request, try again in a few seconds.`
                            });
                          }
                        }
                      }
                    });
                      break;

                    default:
                      break;
                  }
                });
              });
            } else {
              row.addEventListener('click', (e) => {
                let domHTML = document.createElement('DIV');
                    domHTML.innerHTML = `
                      <div class="container">
                        <div class="row">
                          <div class="container">
                            <h4>${(decision['approval'] == 1) ? "APPROVED" : "DECLINED"}</h4>
                            <p>This pay change was descided and signed by Admin</p>
                            <div class="row">
                              <div class="col swal-label">Name</div>
                              <div class="col swal-val">${item.fname} ${item.lname}</div>
                            </div>
                            <div class="row">
                              <div class="col swal-label">PayRate (Old)</div>
                              <div class="col swal-val">£${item.old_rate} per hour</div>
                            </div>
                            <div class="row">
                              <div class="col swal-label">PayRate (New)</div>
                              <div class="col swal-val">£${item.new_rate} per hour</div>
                            </div>
                            <div class="row">
                              <div class="col swal-label">Reason</div>
                              <div class="col swal-val">${item.reason}</div>
                            </div>
                            <br>
                            <h5>Restaurant</h5>
                            <div class="row">
                              <div class="col swal-label">Name</div>
                              <div class="col swal-val">${item.restaurant_name}</div>
                            </div>
                            <div class="row">
                              <div class="col swal-label">Post code</div>
                              <div class="col swal-val">${item.restaurant_postal_code}</div>
                            </div>
                            <div class="row">
                              <div class="col swal-label">City</div>
                              <div class="col swal-val">${item.restaurant_area_name}</div>
                            </div>
                          </div>
                        </div>
                      </div>
                    `;
                swal({
                  title: "Employee Pay change details",
                  content: domHTML
                });
              });
            }
          });
        }
      }
    });


    const payroll_row_template = (arr) => {
      let tr = document.createElement('TR');
      let datetime = arr.datetime;
      let html = `
        <td>E.No-${arr.employee_no}</td>
        <td>${arr.fname} ${arr.lname}</td>
        <td>${arr.restaurant_name}</td>
        <td>${arr.restaurant_area_name}</td>
        <td>${arr.pay_rate}</td>
      `;
      tr.insertAdjacentHTML('beforeend', html);
      return tr;
    };
    // Load all payroll from server
    ajx({
      load: 'up',
      type: 'POST',
      url: './get_payroll',
      data: {},
      success: (response) => {
        // console.log(response);
        if (isJson(response)) {
          let res = JSON.parse(response);
          let rowHolder = document.getElementById('dynamic_payroll_row_holder');
          clearDOM(rowHolder);

          // Process each and every pay change request
          res.forEach((item, i) => {
            // console.log(item);
            let row = payroll_row_template(item);
            rowHolder.append(row);

            row.addEventListener('click', (e) => {
              let payrollHistory = () => { return "This employee has not made any pay change request."; };
              if (item.paychange_history !== null) {
                let hist = item.paychange_history;

                payrollHistory = (history) => {
                  let histHtml = '';
                  history.forEach((arr, i) => {
                    histHtml  += '<div class="row">';
                    histHtml  +=  '<div class="col swal-label" style="font-size: 12px;">'+arr.datetime+'</div>';
                    histHtml  +=  '<div class="col swal-val">£'+arr.old_rate+' <i class="fas fa-arrow-right"></i> £'+arr.new_rate+'</div>';
                    histHtml  +=  '<div class="col swal-val">['+((arr.approval == 1) ? "APPROVED" : ((arr.approval == 0) ? "DECLINED" : "PENDING"))+']</div>';
                    histHtml  += '</div>';
                  });
                  return histHtml;
                };
              }
              let paychange_timeline = payrollHistory(item.paychange_history) || '';
              let domHTML = document.createElement('DIV');
                  domHTML.innerHTML = `
                    <div class="container">
                      <div class="row">
                        <div class="container">
                          <!-- <h4>[TITLE]</h4>
                          <p>[Sub Title]</p> -->
                          <div class="row">
                            <div class="col swal-label">Name</div>
                            <div class="col swal-val">${item.fname} ${item.lname}</div>
                          </div>
                          <div class="row">
                            <div class="col swal-label">PayRate</div>
                            <div class="col swal-val">£${item.pay_rate} per hour</div>
                          </div>
                          <br>
                          <h5>Restaurant</h5>
                          <div class="row">
                            <div class="col swal-label">Name</div>
                            <div class="col swal-val">${item.restaurant_name}</div>
                          </div>
                          <div class="row">
                            <div class="col swal-label">Post code</div>
                            <div class="col swal-val">${item.restaurant_postal_code}</div>
                          </div>
                          <div class="row">
                            <div class="col swal-label">City</div>
                            <div class="col swal-val">${item.restaurant_area_name}</div>
                          </div>
                          <br>
                          <h5>Pay Change History</h5>
                          ${paychange_timeline}
                        </div>
                      </div>
                    </div>
                  `;
              swal({
                title: "Employee details",
                content: domHTML
              });
            });
          });
        }
      }
    });
  })();

  (() => { // Nav menu buttons
    // Request
    let req_nav_btn = document.getElementById('req_nav_btn');
    let req_bdy = document.getElementById('req_body');

    // PayRoll
    let proll_nav_btn = document.getElementById('proll_nav_btn');
    let proll_bdy = document.getElementById('payroll_body');

    // Request
    req_nav_btn.addEventListener('click', (e) => {
      req_nav_btn.classList.add('active');
      req_bdy.dataset.mode = "active";
      proll_nav_btn.classList.remove('active');
      proll_bdy.dataset.mode = "inactive";
    });

    // PayRoll
    proll_nav_btn.addEventListener('click', (e) => {
      req_nav_btn.classList.remove('active');
      req_bdy.dataset.mode = "inactive";
      proll_nav_btn.classList.add('active');
      proll_bdy.dataset.mode = "active";
    });
  })();
});



function sortTable(table, thcol, index) {
  var rows, switching, i, x, y, shouldSwitch, sort;
  sort = thcol.dataset.sort || "desc";
  switching = true;
  while (switching) {
    switching = false;
    rows = table.rows;
    for (i = 1; i < (rows.length - 1); i++) {
      shouldSwitch = false;
      x = rows[i].getElementsByTagName("TD")[index];
      y = rows[i + 1].getElementsByTagName("TD")[index];
      // Descending | Ascending switch
      if (sort == "desc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      } else if (sort == "asc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
    }
  }
  thcol.dataset.sort = (sort == "desc") ? "asc" : "desc";
  let th_children = thcol.parentNode.children;
  for (i = 0; i < th_children.length; i++) {
    if (th_children[i] !== thcol) th_children[i].removeAttribute('data-sort');
  }
}
