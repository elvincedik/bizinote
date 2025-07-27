<template>
  <div v-if="logo">
    <div class="container d-flex justify-content-between align-items-default">
      <div><img :src="logo" :width="60" alt="logo" /></div>
      <div>
        <b-button size="sm" variant="link" @click="showLogin = false"
          >Back to Home</b-button
        >
      </div>
    </div>

    <div class="d-flex auth-layouts-wrap">
      <div class="auth-content">
        <div class="card-ed card o-hidden">
          <div class="row">
            <div class="col-md-12">
              <!-- Register View -->
              <div class="p-4" v-if="!showLogin">
                <div class="mb-30">
                  <p class="text-center font-weight-bold fs56">
                    Join us today 👋
                  </p>
                  <p>
                    Clarity gives you the blocks and components you <br />
                    need to create a truly professional website.
                  </p>
                  <!-- <img :src="logo" alt="logo"> -->
                </div>

                <validation-observer ref="register_form">
                  <b-form @submit.prevent="submitRegister">
                    <!-- Organization Name -->
                    <validation-provider
                      name="Organization Name"
                      rules="required"
                      v-slot="validationContext"
                    >
                      <b-form-group class="text-12">
                        <template #label>
                          <span
                            >Organization Name
                            <span class="text-danger">*</span></span
                          >
                        </template>
                        <b-form-input
                          v-model="organization_name"
                          :state="getValidationState(validationContext)"
                          class="form-control"
                        />
                        <b-form-invalid-feedback>{{
                          validationContext.errors[0]
                        }}</b-form-invalid-feedback>
                      </b-form-group>
                    </validation-provider>

                    <!-- First Name -->
                    <validation-provider
                      name="First Name"
                      rules="required"
                      v-slot="validationContext"
                    >
                      <b-form-group class="text-12">
                        <template #label>
                          <span
                            >First Name <span class="text-danger">*</span></span
                          >
                        </template>
                        <b-form-input
                          v-model="firstname"
                          :state="getValidationState(validationContext)"
                          class="form-control"
                        />
                        <b-form-invalid-feedback>{{
                          validationContext.errors[0]
                        }}</b-form-invalid-feedback>
                      </b-form-group>
                    </validation-provider>

                    <!-- Last Name -->
                    <validation-provider
                      name="Last Name"
                      rules="required"
                      v-slot="validationContext"
                    >
                      <b-form-group class="text-12">
                        <template #label>
                          <span
                            >Last Name <span class="text-danger">*</span></span
                          >
                        </template>
                        <b-form-input
                          v-model="lastname"
                          :state="getValidationState(validationContext)"
                          class="form-control"
                        />
                        <b-form-invalid-feedback>{{
                          validationContext.errors[0]
                        }}</b-form-invalid-feedback>
                      </b-form-group>
                    </validation-provider>

                    <!-- Username -->
                    <validation-provider
                      name="Username"
                      rules="required"
                      v-slot="validationContext"
                    >
                      <b-form-group class="text-12">
                        <template #label>
                          <span
                            >Username <span class="text-danger">*</span></span
                          >
                        </template>
                        <b-form-input
                          v-model="username"
                          :state="getValidationState(validationContext)"
                          class="form-control"
                        />
                        <b-form-invalid-feedback>{{
                          validationContext.errors[0]
                        }}</b-form-invalid-feedback>
                      </b-form-group>
                    </validation-provider>

                    <!-- Email -->
                    <validation-provider
                      name="Email"
                      rules="required|email"
                      v-slot="validationContext"
                    >
                      <b-form-group class="text-12">
                        <template #label>
                          <span>Email <span class="text-danger">*</span></span>
                        </template>
                        <b-form-input
                          v-model="email"
                          :state="getValidationState(validationContext)"
                          type="email"
                          class="form-control"
                        />
                        <b-form-invalid-feedback>{{
                          validationContext.errors[0]
                        }}</b-form-invalid-feedback>
                      </b-form-group>
                    </validation-provider>

                    <!-- Phone (optional) -->
                    <b-form-group class="text-12">
                      <template #label>
                        Phone <span style="color: red">*</span>
                      </template>
                      <b-form-input v-model="phone" class="form-control" />
                    </b-form-group>

                    <!-- Password -->
                    <validation-provider
                      name="Password"
                      rules="required|min:6"
                      v-slot="validationContext"
                    >
                      <b-form-group class="text-12">
                        <template #label>
                          <span
                            >Password <span class="text-danger">*</span></span
                          >
                        </template>
                        <b-form-input
                          v-model="password"
                          type="password"
                          :state="getValidationState(validationContext)"
                          class="form-control"
                        />
                        <b-form-invalid-feedback>{{
                          validationContext.errors[0]
                        }}</b-form-invalid-feedback>
                      </b-form-group>
                    </validation-provider>

                    <div
                      class="d-flex justify-content-between align-items-center"
                    >
                      <div>
                        <b-button
                          type="submit"
                          class="btn nprimary btn-block"
                          variant="primary"
                          :disabled="loading"
                        >
                          Register
                        </b-button>
                      </div>

                      <div>
                        Already have an account?
                        <a
                          href="#"
                          @click.prevent="goToLogin"
                          class="text-muted"
                          ><u>Login</u></a
                        >
                      </div>
                    </div>

                    <div v-if="loading" class="text-center mt-3">
                      <div class="spinner sm spinner-primary"></div>
                    </div>
                  </b-form>
                </validation-observer>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ValidationObserver, ValidationProvider } from "vee-validate";
import { mapGetters } from "vuex";
import NProgress from "nprogress";

export default {
  components: {
    ValidationObserver,
    ValidationProvider,
  },
  data() {
    return {
      organization_name: "",
      firstname: "",
      lastname: "",
      username: "",
      email: "",
      phone: "",
      password: "",
      loading: false,
      logo: null,
    };
  },
  computed: {
    ...mapGetters(["isAuthenticated"]),
  },
  mounted() {
    axios
      .get("/api/get-logo-setting")
      .then((res) => {
        this.logo = res.data.logo
          ? `/images/${res.data.logo}`
          : "/images/logo.png";
      })
      .catch(() => {
        this.logo = "/images/logo.png";
      });
  },
  methods: {
    goToLogin() {
      localStorage.setItem("forceShowLogin", "true");
      window.location.href = "/login";
    },
    submitRegister() {
      this.$refs.register_form.validate().then((valid) => {
        if (!valid) {
          this.makeToast(
            "danger",
            "Please fill all fields correctly",
            "Failed"
          );
          return;
        }

        this.loading = true;
        NProgress.start();
        axios
          .post("/register", {
            organization_name: this.organization_name,
            firstname: this.firstname,
            lastname: this.lastname,
            username: this.username,
            email: this.email,
            password: this.password,
            phone: this.phone,
          })
          .then(() => {
            this.makeToast("success", "Registered Successfully!", "Success");
            window.location.href = "/";
          })
          .catch((error) => {
            console.error("Backend validation error:", error);
          })
          .finally(() => {
            this.loading = false;
            NProgress.done();
          });
      });
    },

    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },

    makeToast(variant, msg, title) {
      this.$root.$bvToast.toast(msg, {
        title: title,
        variant: variant,
        solid: true,
      });
    },
  },
};
</script>
