<script  setup>

import { useRootStore } from '@/stores/root'
import { useTenantAppStore } from '../../stores/store-tenantapps'

const root = useRootStore();
const store = useTenantAppStore();

</script>

<template>

    <Panel :pt="root.panel_pt" >

        <template class="p-1" #header>

            <b class="mr-1">Filters</b>

        </template>

        <template #icons>

            <Button data-testid="tenantsapps-hide-filter"
                    @click="store.toList()"
                    icon="pi pi-times"
                    rounded
                    variant="text"
                    severity="contrast"
                    size="small">
            </Button>

        </template>

        <!--repeatable-->
        <div>
            <b>Sort By:</b>
            <div class="flex items-center gap-2">
                <RadioButton name="sort-none"
                             inputId="sort-none"
                             data-testid="tenantsapps-filters-sort-none"
                             value=""
                             v-model="store.query.filter.sort" />
                <label for="sort-none" class="cursor-pointer">None</label>
            </div>


            <div class="flex items-center gap-2">
                <RadioButton name="sort-ascending"
                             inputId="sort-ascending"
                             data-testid="tenantsapps-filters-sort-ascending"
                             value="updated_at"
                             v-model="store.query.filter.sort" />
                <label for="sort-ascending" class="cursor-pointer">Updated (Ascending)</label>
            </div>

            <div class="flex items-center gap-2">
                <RadioButton name="sort-descending"
                             inputId="sort-descending"
                             data-testid="tenantsapps-filters-sort-descending"
                             value="updated_at:desc"
                             v-model="store.query.filter.sort" />
                <label for="sort-descending" class="cursor-pointer">Updated (Descending)</label>
            </div>


        </div>
        <!--repeatable-->

        <Divider/>

        <!--repeatable-->
        <div>
            <b>Is Active:</b>


            <div class="flex items-center gap-2">
                <RadioButton name="active-all"
                             inputId="active-all"
                             value="null"
                             data-testid="tenantsapps-filters-active-all"
                             v-model="store.query.filter.is_active" />
                <label for="active-all" class="cursor-pointer">All</label>
            </div>



            <div class="flex items-center gap-2">
                <RadioButton name="active-true"
                             inputId="active-true"
                             data-testid="tenantsapps-filters-active-true"
                             value="true"
                             v-model="store.query.filter.is_active" />
                <label for="active-true" class="cursor-pointer">Only Active</label>
            </div>


            <div class="flex items-center gap-2">
                <RadioButton name="active-false"
                             inputId="active-false"
                             data-testid="tenantsapps-filters-active-false"
                             value="false"
                             v-model="store.query.filter.is_active" />
                <label for="active-false" class="cursor-pointer">Only Inactive</label>
            </div>





        </div>
        <!--repeatable-->

        <Divider/>

        <!--repeatable-->
        <div>
            <b>Is Trashed:</b>


            <div class="flex items-center gap-2">
                <RadioButton name="trashed-exclude"
                             inputId="trashed-exclude"
                             data-testid="tenantsapps-filters-trashed-exclude"
                             value=""
                             v-model="store.query.filter.trashed" />
                <label for="trashed-exclude" class="cursor-pointer">Exclude Trashed</label>
            </div>



            <div class="flex items-center gap-2">

                <RadioButton name="trashed-include"
                             inputId="trashed-include"
                             data-testid="tenantsapps-filters-trashed-include"
                             value="include"
                             v-model="store.query.filter.trashed" />
                <label for="trashed-include" class="cursor-pointer">Include Trashed</label>

            </div>



            <div class="flex items-center gap-2">
                <RadioButton name="trashed-only"
                             inputId="trashed-only"
                             data-testid="tenantsapps-filters-trashed-only"
                             value="only"
                             v-model="store.query.filter.trashed" />
                <label for="trashed-only" class="cursor-pointer">Only Trashed</label>
            </div>

        </div>
        <!--repeatable-->

    </Panel>

</template>
