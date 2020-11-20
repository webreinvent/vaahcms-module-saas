<script src="./CreateJs.js"></script>
<template>
    <div class="column" v-if="page.assets">

        <div class="card">

            <!--header-->
            <header class="card-header">

                <div class="card-header-title">
                    Create
                </div>


                <div class="card-header-buttons">

                    <div class="field has-addons is-pulled-right">
                        <p class="control">
                            <b-button icon-left="edit"
                                      type="is-light"
                                      :loading="is_btn_loading"
                                      @click="setLocalAction('save-and-new')">
                                Save & New
                            </b-button>
                        </p>

                        <p class="control">


                            <b-dropdown aria-role="list" position="is-bottom-left">
                                <button class="button is-light" slot="trigger">
                                    <b-icon icon="caret-down"></b-icon>
                                </button>

                                <b-dropdown-item aria-role="listitem"
                                                 @click="setLocalAction('save-and-close')">
                                    <b-icon icon="check"></b-icon>
                                    Save & Close
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 @click="setLocalAction('save-and-clone')">
                                    <b-icon icon="copy"></b-icon>
                                    Save & Clone
                                </b-dropdown-item>

                                <b-dropdown-item aria-role="listitem"
                                                 @click="resetNewItem()">
                                    <b-icon icon="eraser"></b-icon>
                                    Reset
                                </b-dropdown-item>

                            </b-dropdown>


                        </p>

                        <p class="control">
                            <b-button tag="router-link"
                                      type="is-light"
                                      :to="{name: 'servers.list'}"
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

                    <b-field label="Name" :label-position="labelPosition">
                        <b-input name="servers-name" dusk="servers-name" v-model="new_item.name"></b-input>
                    </b-field>

                    <b-field label="Slug" :label-position="labelPosition">
                        <b-input name="servers-slug" dusk="servers-slug" v-model="new_item.slug"></b-input>
                    </b-field>


                    <b-field label="Server Host Type"
                             :label-position="labelPosition">
                        <b-select v-model="new_item.host_type" placeholder="Select a name">
                            <option
                                v-for="(option, index) in page.assets.host_types"
                                :value="option"
                                :key="index">
                                {{ option }}
                            </option>
                        </b-select>
                    </b-field>

                    <b-field label="Database Driver"
                             :label-position="labelPosition">
                        <b-select v-model="new_item.driver" placeholder="Select a name">
                            <option
                                v-for="(option, index) in page.assets.drivers"
                                :value="option"
                                :key="index">
                                {{ option }}
                            </option>
                        </b-select>
                    </b-field>



                    <b-field label="Host" :label-position="labelPosition">
                        <b-input name="servers-host" dusk="servers-host" v-model="new_item.host"></b-input>
                    </b-field>


                    <b-field label="Port" :label-position="labelPosition">
                        <b-input name="servers-port" dusk="servers-port" v-model="new_item.port"></b-input>
                    </b-field>

                    <b-field label="Is Active" :label-position="labelPosition">
                        <b-radio-button name="servers-is_active"
                                        dusk="servers-is_active"
                                        type="is-success"
                                        v-model="new_item.is_active"
                                        :native-value=1>
                            <span>Yes</span>
                        </b-radio-button>

                        <b-radio-button type="is-danger"  name="servers-is_active" dusk="servers-is_active"
                                        v-model="new_item.is_active"
                                        :native-value=0>
                            <span>No</span>
                        </b-radio-button>
                    </b-field>

                    <div v-if="new_item.host_type == 'MySql'">

                        <b-field label="Username" :label-position="labelPosition">
                            <b-input name="servers-username"
                                     autocomplete="new-password"
                                     dusk="servers-username" v-model="new_item.username"></b-input>
                        </b-field>

                        <b-field label="Password" :label-position="labelPosition">
                            <b-input name="servers-password" dusk="servers-password"
                                     autocomplete="new-password"
                                     type="password"
                                     password-reveal
                                     v-model="new_item.password"></b-input>
                        </b-field>



                    </div>


                    <div v-if="new_item.host_type == 'CPanel-MySql'">



                        <b-field label="CPanel Domain" :label-position="labelPosition">
                            <b-input name="servers-host" dusk="servers-host"
                                     v-model="new_item.meta.cpanel_domain"></b-input>
                        </b-field>

                        <b-field label="CPanel API Token" :label-position="labelPosition">
                            <b-input name="servers-cpanel-api-token"
                                     dusk="servers-cpanel-api-token"
                                     v-model="new_item.meta.cpanel_api_token">
                            </b-input>
                        </b-field>

                        <b-field label="CPanel Username" :label-position="labelPosition">
                            <b-input name="servers-cpanel-username"
                                     dusk="servers-cpanel-username"
                                     v-model="new_item.meta.cpanel_username">
                            </b-input>
                        </b-field>

                        <b-field label="CPanel Protocol" :label-position="labelPosition">
                            <b-input name="servers-cpanel-protocol"
                                     dusk="servers-cpanel-protocol"
                                     v-model="new_item.meta.protocol">
                            </b-input>
                        </b-field>


                        <b-field label="CPanel Port" :label-position="labelPosition">
                            <b-input name="servers-cpanel-port"
                                     dusk="servers-cpanel-port"
                                     v-model="new_item.meta.port">
                            </b-input>
                        </b-field>

                    </div>




                    <hr/>

                    <b-field >
                        <b-button type="is-primary"
                                  :loading="is_btn_loading_connect"
                                  @click="connect()"
                                  icon-left="database">
                            Test Database Connection
                        </b-button>
                    </b-field>


                </div>
            </div>
            <!--/content-->





        </div>




    </div>
</template>


