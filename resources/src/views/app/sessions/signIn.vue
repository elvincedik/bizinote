<template>
  <div class="auth-layout-wrap" v-if="logo">
    <div class="auth-content">
      <div class="card o-hidden">
        <div class="row">
          <div class="col-md-12">

            <!-- Homepage View -->
            <div class="p-4 text-center" v-if="!showLogin">
              <h1>Welcome to Tenant System</h1>
              <p>Manage your rental property and tenants efficiently.</p>
              <b-button class="btn btn-primary m-2" @click="showLogin = true">Login</b-button>
              <b-button class="btn btn-outline-secondary m-2" href="/register">Register</b-button>
            </div>

            <!-- Login Form View -->
            <div class="p-4" v-else>
              <div class="auth-logo text-center mb-30">
                <img :src="logo" alt="logo">
              </div>
              <h1 class="mb-3 text-18">{{$t('SignIn')}}</h1>

              <validation-observer ref="submit_login">
                <b-form @submit.prevent="Submit_Login">
                  
                  <!-- Email -->
                  <validation-provider name="Email Address" :rules="{ required: true }" v-slot="validationContext">
                    <b-form-group :label="$t('Email_Address')" class="text-12">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="Email-feedback"
                        class="form-control-rounded"
                        type="text"
                        v-model="email"
                      ></b-form-input>
                      <b-form-invalid-feedback id="Email-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>

                  <!-- Password -->
                  <validation-provider name="Password" :rules="{ required: true }" v-slot="validationContext">
                    <b-form-group :label="$t('password')" class="text-12">
                      <b-form-input
                        :state="getValidationState(validationContext)"
                        aria-describedby="Password-feedback"
                        class="form-control-rounded"
                        type="password"
                        v-model="password"
                      ></b-form-input>
                      <b-form-invalid-feedback id="Password-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                    </b-form-group>
                  </validation-provider>

                  <b-button
                    type="submit"
                    tag="button"
                    class="btn-rounded btn-block mt-2"
                    variant="primary mt-2"
                    :disabled="loading"
                  >{{$t('SignIn')}}</b-button>

                  <div v-once class="typo__p" v-if="loading">
                    <div class="spinner sm spinner-primary mt-3"></div>
                  </div>

                </b-form>
              </validation-observer>

              <div class="mt-3 text-center">
                <a href="/password/reset" class="text-muted">
                  <u>{{$t('Forgot_Password')}}</u>
                </a>
              </div>

              <div class="mt-3 text-center">
                Don't have an account?
                <a href="/register" class="text-muted">
                  <u>{{$t('Register')}}</u>
                </a>
              </div>

              <div class="mt-3 text-center">
                <b-button size="sm" variant="link" @click="showLogin = false">Back to Home</b-button>
              </div>

            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import NProgress from "nprogress";
import axios from "axios";

export default {
  metaInfo: {
    title: "SignIn"
  },
  data() {
    return {
      email: "",
      password: "",
      userId: "",
      loading: false,
      logo: null,
      showLogin: false, // Added for toggling views
    };
  },
  computed: {
    ...mapGetters(["isAuthenticated", "error"])
  },
  mounted() {
    axios.get("/api/get-logo-setting")
      .then(response => {
        this.logo = response.data.logo
          ? `/images/${response.data.logo}`
          : "/images/logo.png";
      })
      .catch(() => {
        this.logo = "/images/logo.png";
      });
  },
  methods: {
    // Form submission
    Submit_Login() {
      this.$refs.submit_login.validate().then(success => {
        if (!success) {
          this.makeToast("danger", this.$t("Please_fill_the_form_correctly"), this.$t("Failed"));
        } else {
          this.Login();
        }
      });
    },

    // Validation state
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },

    // Login method
    Login() {
      let self = this;
      NProgress.start();
      NProgress.set(0.1);
      self.loading = true;

      axios.post("/login", {
          email: self.email,
          password: self.password
        })
        .then(() => {
          this.makeToast("success", this.$t("Successfully_Logged_In"), this.$t("Success"));
          window.location = '/';
          NProgress.done();
          this.loading = false;
        })
        .catch(() => {
          NProgress.done();
          this.loading = false;
          this.makeToast("danger", this.$t("Incorrect_Login"), this.$t("Failed"));
        });
    },

    // Toast helper
    makeToast(variant, msg, title) {
      this.$root.$bvToast.toast(msg, {
        title: title,
        variant: variant,
        solid: true
      });
    }
  }
};
</script>

<style scoped>
/* Optional: Add any extra styles here if needed */
</style>
