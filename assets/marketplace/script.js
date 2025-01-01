document.addEventListener('DOMContentLoaded', function () {
    const toggles = document.querySelectorAll('.toggle-item');
    toggles.forEach(toggle => {
        toggle.addEventListener('change', function () {
            const label = document.querySelector(`label[for="${this.id}"]`);

            if (this.checked) {
                label.classList.add('active');
            } else {
                label.classList.remove('active');
            }
        });
    });
});






function generateModal(plugin) {
    let modalPop = document.querySelector(".modalPopUp");
    modalPop.classList.add("active")
    console.log(plugin);
    let HTML = `

    <div class="modalContainer"> 
    
    <button class="close-modal">
    <svg width="42" height="42" viewBox="0 0 42 42" fill="none" xmlns="http://www.w3.org/2000/svg">
    <g filter="url(#filter0_d_22_29)">
    <rect x="4" width="34" height="34" rx="17" fill="white"/>
    <rect x="4.5" y="0.5" width="33" height="33" rx="16.5" stroke="white"/>
    </g>
    <g clip-path="url(#clip0_22_29)">
    <path d="M21 32.4546C29.5353 32.4546 36.4545 25.5353 36.4545 17C36.4545 8.46471 29.5353 1.54547 21 1.54547C12.4646 1.54547 5.54541 8.46471 5.54541 17C5.54541 25.5353 12.4646 32.4546 21 32.4546Z" fill="#EBEBEB"/>
    <path d="M14 10L21 17L28 24M28 10L14 24" stroke="#2C2C2C" stroke-width="5" stroke-linecap="round"/>
    </g>
    <defs>
    <filter id="filter0_d_22_29" x="0" y="0" width="42" height="42" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
    <feFlood flood-opacity="0" result="BackgroundImageFix"/>
    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
    <feOffset dy="4"/>
    <feGaussianBlur stdDeviation="2"/>
    <feComposite in2="hardAlpha" operator="out"/>
    <feColorMatrix type="matrix" values="0 0 0 0 0.29625 0 0 0 0 0.297417 0 0 0 0 0.3 0 0 0 0.521569 0"/>
    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_22_29"/>
    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_22_29" result="shape"/>
    </filter>
    <clipPath id="clip0_22_29">
    <rect width="30.9091" height="30.9091" fill="white" transform="translate(5.54541 1.54547)"/>
    </clipPath>
    </defs>
    </svg>
    </button>

    <div class="modal-img">
        <img src="${plugin.image_url}">
    </div>

    <div class="modal_header">
        <div class="modal-content">
            <h3 class="plugin-title">${plugin.name}</h3>
            <p class="plugin-author">${plugin.author}</p>
            <div class="plugin-info modal-plugin-info">
               <div class="pluginupdate">  <span class=""><strong>Updated</strong>:5 days ago</span> </div>
                <div class="pluginv">  <span class=""><strong>version</strong>1.3</span> </div>
            </div>
        </div>
        <div>
        <button class="plugin-download _btn">
                        <svg width="15" height="15" viewBox="0 0 15 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.5 15C11.6421 15 15 11.6421 15 7.5C15 3.35786 11.6421 0 7.5 0C3.35786 0 0 3.35786 0 7.5C0 11.6421 3.35786 15 7.5 15Z" fill="#2961DB"/>
                    <g opacity="0.2">
                    <path d="M11.25 7.5C11.25 5.97656 10.3594 4.6875 9.04687 4.10156C8.64844 3.91406 8.4375 4.21875 8.4375 4.47656V6.46875C8.4375 6.65625 8.60156 6.79687 8.78906 6.79687H9.51562C9.86719 6.79687 9.96094 7.05468 9.72656 7.35937L7.92187 9.65625C7.6875 9.9375 7.3125 9.9375 7.10156 9.65625L5.29688 7.35937C5.0625 7.07812 5.15625 6.79687 5.50781 6.79687H6.23438C6.42188 6.79687 6.58594 6.65625 6.58594 6.46875V4.47656C6.58594 4.21875 6.23437 3.98437 5.92969 4.10156C4.92187 4.57031 4.14844 5.48437 3.89063 6.58594C2.74219 6.77344 1.875 7.73437 1.875 8.90625C1.875 10.1953 2.92969 11.25 4.21875 11.25H11.25C12.2813 11.25 13.125 10.4062 13.125 9.375C13.125 8.34375 12.2813 7.5 11.25 7.5Z" fill="#231F20"/>
                    </g>
                    <path d="M11.25 7.03125C11.25 5.50781 10.3594 4.21875 9.04687 3.63281C8.64844 3.44531 8.4375 3.75 8.4375 4.00781V6C8.4375 6.1875 8.60156 6.32812 8.78906 6.32812H9.51562C9.86719 6.32812 9.96094 6.58593 9.72656 6.89062L7.92187 9.1875C7.6875 9.46875 7.3125 9.46875 7.10156 9.1875L5.29688 6.89062C5.0625 6.60937 5.15625 6.32812 5.50781 6.32812H6.23438C6.42188 6.32812 6.58594 6.1875 6.58594 6V4.00781C6.58594 3.75 6.23437 3.51562 5.92969 3.63281C4.92187 4.10156 4.14844 5.01562 3.89063 6.11719C2.74219 6.30469 1.875 7.26562 1.875 8.4375C1.875 9.72656 2.92969 10.7812 4.21875 10.7812H11.25C12.2813 10.7812 13.125 9.9375 13.125 8.90625C13.125 7.875 12.2813 7.03125 11.25 7.03125Z" fill="white"/>
                    </svg>
                    download
         </button>
         </div>
    </div>
    
    <div class="modal-tabs">
        <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-Description-tab" data-bs-toggle="tab" data-bs-target="#nav-Description" type="button" role="tab" aria-controls="nav-Description" aria-selected="true">Description</button>
            <button class="nav-link" id="nav-Changelog-tab" data-bs-toggle="tab" data-bs-target="#nav-Changelog" type="button" role="tab" aria-controls="nav-Changelog" aria-selected="false">Changelog</button>
            <button class="nav-link" id="nav-FAQ-tab" data-bs-toggle="tab" data-bs-target="#nav-FAQ" type="button" role="tab" aria-controls="nav-FAQ" aria-selected="false">FAQ</button>
            <button class="nav-link" id="nav-developer-tab" data-bs-toggle="tab" data-bs-target="#nav-developer" type="button" role="tab" aria-controls="nav-developer" aria-selected="false">developer</button>
        </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-Description" role="tabpanel" aria-labelledby="nav-Description-tab" tabindex="0">${plugin.sections.description}</div>
        <div class="tab-pane fade" id="nav-Changelog" role="tabpanel" aria-labelledby="nav-Changelog-tab" tabindex="0">${plugin.sections.changelog}</div>
        <div class="tab-pane fade" id="nav-FAQ" role="tabpanel" aria-labelledby="nav-FAQ-tab" tabindex="0">FAQ content goes here...</div>
        <div class="tab-pane fade" id="nav-developer" role="tabpanel" aria-labelledby="nav-developer-tab" tabindex="0">developer content goes here...</div>
        </div>
    </div>

    </div>
    `;

    modalPop.innerHTML = HTML;

    modalTap()
    closeModal()
}

function modalTap() {
    const tabButtons = document.querySelectorAll('.nav-tabs .nav-link');
    const tabPanes = document.querySelectorAll('.tab-pane');
    console.log(tabButtons);
    console.log(tabPanes);
    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabPanes.forEach(pane => pane.classList.remove('show', 'active'));
            this.classList.add('active');
            const targetPane = document.querySelector(this.getAttribute('data-bs-target'));
            targetPane.classList.add('show', 'active');
        });
    });

}

function closeModal() {
    document.querySelector(".close-modal").addEventListener("click", function () {
        document.querySelector(".modalPopUp").classList.remove('active');
    })
    document.querySelector(".modalPopUp").addEventListener("click", function (e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });

}


function handleModal(id) {
    jQuery.ajax({
        url: PluginData.ajaxurl,
        type: 'POST',
        data: {
            action: 'dvi_modal_action',
            nonce: PluginData.nonce
        },
        success: function(response) {
            data = JSON.parse(response);
            // console.log(data.pluginsData);
            for (const key in data.pluginsData) {
                console.log(key);
                console.log(id);
                
                if (key === id.toString()) {
                    const plugin =data.pluginsData[key]
                    console.log('Found:', plugin);
                    generateModal(plugin)
                    break;
                }
            }
            // const plugin = data.pluginsData.find(plugin => console.log(plugin) )
        },
        error: function(jqXHR, textStatus, errorThrown) {
            button.text('Install Failed: ' + textStatus);
            button.prop('disabled', false);
        }
    });
}
jQuery(document).ready(function($) {

   $(document).on('click', '.plugin-download', function(e) {
    e.preventDefault();

    let downloadUrl = $(this).data('download-url');
    let pluginName = $(this).data('plugin-name');
    let button = $(this);

    button.prop('disabled', true).text('Installing...');

    $.ajax({
        url: PluginData.ajaxurl,
        type: 'POST',
        data: {
            action: 'dvi_install_plugin',
            download_url: downloadUrl,
            nonce: PluginData.nonce
        },
        success: function(response) {
            console.log("Raw server response:", response);

            if (response.success) {
                button.text('Plugin installed successfully - Activate it now');
                console.log("Plugin installed successfully");
                button.addClass('update-plugin');
            } else {
                if (response.data != undefined) {
                    button.text(response.data);
                } else {
                    button.text('Install Failed');
                }
                console.error("Installation error:", response.data);
            }
            button.prop('disabled', true);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX error:" + errorThrown);
            button.text('Install Failed: ' + textStatus);
            button.prop('disabled', false);
        }
    });
});

$(document).on('click', '.plugin-update', function(e) {
    e.preventDefault();

    let plugin_path = $(this).data('plugin-path');
    let button = $(this);
    let slug = $(this).data('slug');

    button.prop('disabled', true).text('Installing...');

    $.ajax({
        url: PluginData.ajaxurl,
        type: 'POST',
        data: {
            action: 'update-plugin',
            plugin: plugin_path,
            slug:slug,
            _ajax_nonce: PluginData.nonce_updates
        },
        success: function(response) {
            console.log("Raw server response:", response);

            if (response.success) {
                button.text('Plugin installed successfully - Activate it now');
                console.log("Plugin installed successfully");
                button.addClass('update-plugin');
            } else {
                if (response.data != undefined) {
                    button.text(response.data);
                } else {
                    button.text('Install Failed');
                }
                console.error("Installation error:", response.data);
            }
            button.prop('disabled', true);
        },
        error: function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX error:" + errorThrown);
            button.text('Install Failed: ' + textStatus);
            button.prop('disabled', false);
        }
    });
});
 
    $('.active-plugin label').on('click',function(e) {
        e.preventDefault();

        let plugin_info = $(this).data('plugin_info');
        let plugin_active = $(this).data('plugin_active');
        let button = $(this);

        button.prop('disabled', true);

        $.ajax({
            url: PluginData.ajaxurl,
            type: 'POST',
            data: {
                action: 'activate_plugin',
                plugin_info: plugin_info,
                plugin_active: plugin_active,
                nonce: PluginData.nonce
            },
            success: function(response) {
                console.log("server response:", response);
                button.prop('disabled', false).toggleClass('active');
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:" + errorThrown);
                button.text('Install Failed: ' + textStatus);
                button.prop('disabled', false);
            }
        });
        
    });
});