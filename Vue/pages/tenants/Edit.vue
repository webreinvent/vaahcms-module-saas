<script src="./EditJs.js"></script>
<template>
    <div class="column" v-if="page.assets && item">

        <div class="card">

            <!--header-->
            <header class="card-header">

                <div class="card-header-title">
                    <span>{{$vaah.limitString(title, 15)}}</span>
                </div>


                <div class="card-header-buttons">

                    <div class="field has-addons is-pulled-right">
                        <p class="control">
                            <b-button @click="$vaah.copy(item.id)"  type="is-light">
                                <small><b>#{{item.id}}</b></small>
                            </b-button>
                        </p>

                        <p class="control">
                            <b-button icon-left="save"
                                      type="is-light"
                                      :loading="is_btn_loading"
                                      @click="setLocalAction('save')">
                                Save
                            </b-button>
                        </p>

                        <p class="control">


                            <b-dropdown aria-role="list" position="is-bottom-left">
                                <button class="button is-light"
                                        slot="trigger">
                                    <b-icon icon="caret-down"></b-icon>
                                </button>

                                <b-dropdown-item aria-role="listitem"
                                                 @click="setLocalAction('save-and-close')">
                                    <b-icon icon="check"></b-icon>
                                    Save & Close
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 @click="setLocalAction('save-and-new')">
                                    <b-icon icon="plus"></b-icon>
                                    Save & New
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 @click="setLocalAction('save-and-clone')">
                                    <b-icon icon="copy"></b-icon>
                                    Save & Clone
                                </b-dropdown-item>

                            </b-dropdown>


                        </p>

                        <p class="control">
                            <b-button tag="router-link"
                                      type="is-light"
                                      :to="{name: 'tenants.view', params:{id:item.id}}"
                                      icon-left="times">
                            </b-button>
                        </p>



                    </div>


                </div>

            </header>
            <!--/header-->

            <!--content-->
            <div class="card-content">
                <div class="block">

                    <b-field label="Search & Select Server"
                             :label-position="labelPosition">
                        <AutoCompleteAjax v-if="item.server"
                                          label="Search Server"
                                          :selected_value="item.server.name"
                                          :ajax_url="ajax_url_server_search"
                                          @onSelect="updateServerId"
                                          dusk="tenants-server">
                        </AutoCompleteAjax>

                        <AutoCompleteAjax v-else
                                          label="Search Server"
                                          :ajax_url="ajax_url_server_search"
                                          @onSelect="updateServerId"
                                          dusk="tenants-server">
                        </AutoCompleteAjax>

                    </b-field>



                    <b-field label="Name" :label-position="labelPosition">
                        <b-input name="tenants-name" dusk="tenants-name"
                                 v-model="item.name"></b-input>
                    </b-field>

                    <b-field label="Slug" :label-position="labelPosition">
                        <b-input name="tenants-slug" dusk="tenants-slug"
                                 v-model="item.slug"></b-input>
                    </b-field>

                    <b-field label="Path" :label-position="labelPosition">
                        <b-input name="tenants-path" dusk="tenants-path"
                                 v-model="item.path"></b-input>
                    </b-field>

                    <b-field label="Domain" :label-position="labelPosition">
                        <b-input name="tenants-domain" dusk="tenants-domain"
                                 v-model="item.domain"></b-input>
                    </b-field>


                    <b-field label="Sub Domain" :label-position="labelPosition">
                        <b-input name="tenants-sub_domain" dusk="tenants-sub_domain"
                                 v-model="item.sub_domain"></b-input>
                    </b-field>

                    <b-field label="Database Name" :label-position="labelPosition">
                        <b-input name="tenants-database_name" dusk="tenants-database_name"
                                 v-model="item.database_name"></b-input>
                    </b-field>

                    <b-field label="Database Username" :label-position="labelPosition">
                        <b-input name="tenants-database_username" dusk="tenants-database_username"
                                 v-model="item.database_username"></b-input>
                    </b-field>

                    <b-field label="New Database User Password" :label-position="labelPosition">
                        <b-input name="tenants-database_password"
                                 dusk="tenants-database_password"
                                 password-reveal
                                 type="password"
                                 v-model="new_password"></b-input>
                    </b-field>


                    <b-field label="Database Charset" :label-position="labelPosition">
                        <b-input name="tenants-database_charset" dusk="tenants-database_charset"
                                 v-model="item.database_charset"></b-input>
                    </b-field>


                    <b-field label="Database Collation" :label-position="labelPosition">
                        <b-input name="tenants-database_collation" dusk="tenants-database_collation"
                                 v-model="item.database_collation"></b-input>
                    </b-field>


                    <b-field label="Database SSL Mode"
                             :label-position="labelPosition">
                        <b-select v-model="item.database_sslmode" placeholder="Select a name">
                            <option
                                v-for="(option, index) in page.assets.database_sslmodes"
                                :value="option"
                                :key="index">
                                {{ option }}
                            </option>
                        </b-select>
                    </b-field>



                    <div class="block" v-if="item.database_sslmode && item.database_sslmode != 'disable'">

                        <b-field label="SSL Key Path" :label-position="labelPosition">
                            <b-input name="tenant-ssl_key_path"
                                     v-model="item.meta.ssl_key_path"></b-input>
                        </b-field>

                        <b-field label="CERT Path" :label-position="labelPosition">
                            <b-input name="tenant-cert_path"
                                     v-model="item.meta.cert_path"></b-input>
                        </b-field>

                        <b-field label="SSL CA Path" :label-position="labelPosition">
                            <b-input name="tenant-ssl_ca_path"
                                     v-model="item.meta.ssl_ca_path"></b-input>
                        </b-field>

                    </div>

                    <b-field label="Is Active" :label-position="labelPosition">
                        <b-radio-button name="tenants-is_active"
                                        dusk="tenants-is_active"
                                        type="is-success"
                                        v-model="item.is_active"
                                        :native-value=1>
                            <span>Yes</span>
                        </b-radio-button>

                        <b-radio-button type="is-danger"  name="tenants-is_active"
                                        dusk="tenants-is_active"
                                        v-model="item.is_active"
                                        :native-value=0>
                            <span>No</span>
                        </b-radio-button>
                    </b-field>

                    <b-field label="Notes" :label-position="labelPosition">
                        <b-input name="tenants-notes"
                                 type="textarea"
                                 dusk="tenants-notes"
                                 v-model="item.notes"></b-input>
                    </b-field>


                </div>
            </div>
            <!--/content-->





        </div>




    </div>
</template>


