<script src="./BackendJs.js"></script>

<template>

    <!--sections-->
    <div class="container">

        <!--columns-->
        <div class="columns">
            <div class="column is-2">
                <AsideMenu/>
            </div>

            <div class="column is-10">

                <router-view></router-view>

            </div>
        </div>
        <!--/columns-->

    </div>
    <!--sections-->


</template>

