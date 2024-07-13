<template>
    <page-welcome>
        <template
            v-slot:default="{ authenticate, challenge, finduser, resetpass }"
        >
            <v-sheet v-if="forgotPassword">
                <v-toolbar>
                    <v-toolbar-title>Cari Akun Anda</v-toolbar-title>
                </v-toolbar>

                <v-card-text>
                    <v-row v-if="!resetFeature">
                        <v-col cols="12">
                            <v-text-field
                                label="Pengguna"
                                placeholder="Masukan NIP/NIK"
                                hide-details
                                v-model="email"
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row v-if="resetFeature">
                        <v-col cols="12">
                            <v-text-field
                                :append-inner-icon="
                                    visible ? 'visibility' : 'visibility_off'
                                "
                                :type="visible ? 'text' : 'password'"
                                label="Sandi Baru"
                                placeholder="Masukan Katasandi"
                                v-model="password"
                                hide-details
                                @click:append-inner="visible = !visible"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-text-field
                                :append-inner-icon="
                                    visible ? 'visibility' : 'visibility_off'
                                "
                                :type="visible ? 'text' : 'password'"
                                label="Konfirmasi"
                                placeholder="Masukan Katasandi"
                                v-model="password_confirmation"
                                hide-details
                                @click:append-inner="visible = !visible"
                            ></v-text-field>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-card-text class="pt-2 pb-7">
                    <v-row dense>
                        <v-col cols="6">
                            <v-btn
                                block
                                color="primary"
                                size="large"
                                @click="
                                    forgotPassword = !forgotPassword;
                                    resetFeature = false;
                                "
                                >Batal</v-btn
                            >
                        </v-col>

                        <v-col cols="6">
                            <v-btn
                                v-if="!resetFeature"
                                block
                                color="primary"
                                size="large"
                                @click="finduser"
                                >Cari</v-btn
                            >

                            <v-btn v-else block color="primary" size="large">
                                Reset

                                <v-dialog activator="parent" max-width="340">
                                    <template v-slot:default="{ isActive }">
                                        <v-sheet>
                                            <v-toolbar>
                                                <v-toolbar-title
                                                    >OTP Token</v-toolbar-title
                                                >
                                            </v-toolbar>

                                            <v-card-text class="pb-2">
                                                <p
                                                    class="text-justify px-2 pt-1 pb-2"
                                                >
                                                    Silahkan masukan token dari
                                                    aplikasi Authenticator untuk
                                                    dapat melakukan perubahan
                                                    sandi.
                                                </p>

                                                <v-otp-input
                                                    label="OTP"
                                                    length="6"
                                                    v-model="twoFactorCode"
                                                    hide-details
                                                ></v-otp-input>
                                            </v-card-text>

                                            <v-divider></v-divider>

                                            <div class="d-flex px-6 py-4">
                                                <v-spacer></v-spacer>

                                                <v-btn
                                                    class="ml-auto"
                                                    text="Batal"
                                                    @click="
                                                        isActive.value = false
                                                    "
                                                ></v-btn>

                                                <v-btn
                                                    class="ml-2"
                                                    text="Kirim"
                                                    @click="
                                                        resetpass(
                                                            () =>
                                                                (isActive.value = false)
                                                        )
                                                    "
                                                ></v-btn>
                                            </div>
                                        </v-sheet>
                                    </template>
                                </v-dialog>
                            </v-btn>
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-sheet>

            <v-sheet v-else>
                <v-toolbar :color="`${theme}-darken-1`">
                    <v-toolbar-title v-if="!twoFactor"
                        >Otentikasi Pengguna</v-toolbar-title
                    >
                    <v-toolbar-title v-else>Otentikasi OTP</v-toolbar-title>
                </v-toolbar>

                <v-card-text>
                    <v-row v-if="!twoFactor">
                        <v-col cols="12">
                            <v-text-field
                                label="Pengguna"
                                placeholder="Masukan NIP/NIK"
                                hide-details
                                v-model="email"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-text-field
                                :append-inner-icon="
                                    visible ? 'visibility' : 'visibility_off'
                                "
                                :type="visible ? 'text' : 'password'"
                                label="Katasandi"
                                placeholder="Masukan Katasandi"
                                v-model="password"
                                hide-details
                                @click:append-inner="visible = !visible"
                            ></v-text-field>
                        </v-col>
                    </v-row>

                    <v-row dense v-else>
                        <v-col cols="12" v-if="twoModeCode">
                            <v-otp-input
                                label="OTP"
                                length="6"
                                v-model="twoFactorCode"
                                hide-details
                            ></v-otp-input>
                        </v-col>

                        <v-col cols="12" v-else>
                            <v-text-field
                                class="my-3"
                                label="Kode Pemulihan"
                                hide-details
                                v-model="twoFactorCode"
                            ></v-text-field>
                        </v-col>

                        <v-col cols="12">
                            <v-switch
                                v-model="twoModeCode"
                                :label="
                                    twoModeCode
                                        ? `Mode Pemulihan`
                                        : `Mode Token`
                                "
                                hide-details
                                inset
                            ></v-switch>
                        </v-col>
                    </v-row>
                </v-card-text>

                <v-card-text>
                    <div
                        class="text-blue font-weight-medium text-center my-4"
                        @click="forgotPassword = !forgotPassword"
                    >
                        Lupa Password?
                    </div>

                    <v-btn
                        v-if="!twoFactor"
                        block
                        :color="theme"
                        size="large"
                        @click="authenticate"
                        >Masuk</v-btn
                    >

                    <v-row dense v-else>
                        <v-col cols="4">
                            <v-btn
                                block
                                color="primary"
                                size="large"
                                @click="twoFactor = !twoFactor"
                                >Batal</v-btn
                            >
                        </v-col>

                        <v-col cols="8">
                            <v-btn
                                block
                                color="primary"
                                size="large"
                                @click="challenge"
                                >Konfirmasi</v-btn
                            >
                        </v-col>
                    </v-row>
                </v-card-text>
            </v-sheet>
        </template>
    </page-welcome>
</template>

<script>
import { usePageStore } from "@pinia/pageStore";
import { storeToRefs } from "pinia";

export default {
    name: "welcome-page",

    setup() {
        const store = usePageStore();

        const {
            email,
            forgotPassword,
            password,
            password_confirmation,
            resetFeature,
            twoFactor,
            twoFactorCode,
            twoFactorMode,
            twoModeCode,
            theme,
        } = storeToRefs(store);

        return {
            email,
            forgotPassword,
            password,
            password_confirmation,
            resetFeature,
            twoFactor,
            twoFactorCode,
            twoFactorMode,
            twoModeCode,
            theme,
        };
    },

    data: () => ({
        visible: false,
    }),
};
</script>
