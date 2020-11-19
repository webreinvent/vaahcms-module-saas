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
                                      :to="{name: 'apps.list'}"
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


                    <b-field label="App Types"
                             :label-position="labelPosition">
                        <b-select v-model="new_item.app_type"

                                  placeholder="Select a App Type">
                            <option
                                v-for="(option, index) in page.assets.app_types"
                                :value="option"
                                :key="index">
                                {{ option }}
                            </option>
                        </b-select>
                    </b-field>

                    <b-field label="Module Name"
                             v-if="new_item.app_type == 'Module'"
                             :label-position="labelPosition">
                        <b-select v-model="new_item.name"
                                  @input="setNewItemValues()"
                                  placeholder="Select module">
                            <option
                                v-for="(option, index) in page.assets.modules"
                                :value="option.name"
                                :key="index">
                                {{ option.name }}
                            </option>
                        </b-select>
                    </b-field>

                    <b-field label="Name" v-else :label-position="labelPosition">
                        <b-input name="apps-name" dusk="apps-name" v-model="new_item.name"></b-input>
                    </b-field>

                    <b-field label="Slug" :label-position="labelPosition">
                        <b-input name="apps-slug" dusk="apps-slug" v-model="new_item.slug"></b-input>
                    </b-field>



                    <b-field label="Relative Path" :label-position="labelPosition">
                        <b-input name="apps-relative_path" dusk="apps-relative_path"
                                 v-model="new_item.relative_path"></b-input>
                    </b-field>

                    <b-field label="Migration Path" :label-position="labelPosition">
                        <b-input name="apps-migration_path" dusk="apps-migration_path"
                                 v-model="new_item.migration_path"></b-input>
                    </b-field>

                    <b-field label="Seed Class" :label-position="labelPosition">
                        <b-input name="apps-seed_class" dusk="apps-seed_class"
                                 v-model="new_item.seed_class"></b-input>
                    </b-field>


                    <b-field label="Sample Data Class" :label-position="labelPosition">
                        <b-input name="apps-sample_data_class" dusk="apps-sample_data_class"
                                 v-model="new_item.sample_data_class"></b-input>
                    </b-field>

                    <b-field label="Excerpt" :label-position="labelPosition">
                        <b-input name="apps-excerpt" dusk="apps-excerpt"
                                 type="textarea"
                                 maxlength="200"
                                 v-model="new_item.excerpt"></b-input>
                    </b-field>


                    <b-field label="Notes" :label-position="labelPosition">
                        <b-input name="apps-notes" dusk="apps-notes"
                                 type="textarea"
                                 maxlength="200"
                                 v-model="new_item.notes"></b-input>
                    </b-field>


                </div>
            </div>
            <!--/content-->





        </div>




    </div>
</template>


