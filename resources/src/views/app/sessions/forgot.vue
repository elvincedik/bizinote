<template>
  <div v-if="logo">
    <div class="container d-flex justify-content-between align-items-default">
      <div><img :src="logo" :width="60" alt="logo" /></div>
      <div>
        <b-button size="sm" variant="link" @click="showLogin = false"
          ><a href="/login">Back to Home</a></b-button
        >
      </div>
    </div>
   <div class="auth-layoutss-wrap mt-5">
    <div class="auth-content">
      <div class="card-ed o-hidden">
        <div class="row">
          <div class="col-md-12">
            <div class="p-4">
              <div class="auth-logo text-center mt-44">
                 <!-- <img :src="logo" alt="logo"> -->
              </div>
              <div class="mb-4"><h1 class="text-33 font-weight-bold">{{$t('Forgot_Password')}}</h1>
              <p>Enter your email address to set new password</p></div>
              
              <validation-observer ref="Reset_password">
                <b-form @submit.prevent="Submit_Reset">
                  
               <validation-provider
  name="Email Address"
  :rules="{ required: true, email: true }"
  v-slot="validationContext"
>
  <b-form-group :label="$t('Email_Address') + ' '">
    <template #label>
      <span>{{ $t('Email_Address') }}</span>
      <span style="color: red;">*</span>
    </template>
    <b-form-input
      :state="getValidationState(validationContext)"
      aria-describedby="Email-feedback"
      class="form-control"
      type="text"
      v-model="email"
    ></b-form-input>
    <b-form-invalid-feedback id="Email-feedback">
      {{ validationContext.errors[0] }}
    </b-form-invalid-feedback>
  </b-form-group>
</validation-provider>

                  <!-- <button
                    type="submit"
                    :disabled="loading"
                    class="btn btn-primary btn-block nprimary mt-3"
                  >{{$t('Reset_Password')}}</button> -->
                  <div v-once class="typo__p" v-if="loading">
                    <div class="spinner sm spinner-primary mt-3"></div>
                  </div>
                </b-form>
              </validation-observer>
              <!-- <div class="mt-3 text-center">
                <a href="/login"  class="text-muted">
                  <u>{{$t('SignIn')}}</u>
                </a>
              </div> -->

                 <div
                                            class="d-flex justify-content-between align-items-center"
                                        >
                                            <div>
                                                <b-button
                                                    type="submit"
                                                    tag="button"
                                                    class="btn nprimary btn33 mt-2"
                                                    variant="primary mt-2"
                                                    :disabled="loading"
                                                    >Next</b-button
                                                >
                                            </div>

                                            <div>
      
                                                <a
                                                    href="/login"
                                                    class="nprimarytext"
                                                >
                                                    <u>Back to login</u>
                                                </a>
                                            </div>
                                        </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</template>

<script>
import NProgress from "nprogress";

export default {
  metaInfo: {
    // if no subcomponents specify a metaInfo.title, this title will be used
    title: "Forgot Password"
  },
  data() {
    return {
      email: "",
      loading: false,
      logo: null,
    };
  },

  mounted() {
    axios.get("/api/get-logo-setting")
      .then(response => {
        this.logo = response.data.logo
          ? `/images/${response.data.logo}`
          : "/images/logo.png"; // fallback
      })
      .catch(() => {
        this.logo = "/images/logo.png";
      });
  },
  methods: {
    //------------- Submit Reset Password
    Submit_Reset() {
      this.$refs.Reset_password.validate().then(success => {
        if (!success) {
          this.makeToast(
            "danger",
            this.$t("Please_fill_the_Email_Adress"),
            this.$t("Failed")
          );
        } else {
          this.Reset_Password();
        }
      });
    },

    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },

    //------ Toast
    makeToast(variant, msg, title) {
      this.$root.$bvToast.toast(msg, {
        title: title,
        variant: variant,
        solid: true
      });
    },

    Reset_Password() {
      // Start the progress bar.
      NProgress.start();
      NProgress.set(0.1);
      this.loading = true;
      axios
        .post("/api/password/create", { email: this.email }
        )
        .then(result => {
          // this.response = result.data;

          if (result.data.status) {
            this.makeToast(
              "success",
              this.$t("We_have_emailed_your_password_reset_link"),
              this.$t("Success")
            );
       
          } else if (!result.data.status) {
            this.makeToast(
              "danger",
              this.$t("We_cant_find_a_user_with_that_email_addres"),
              this.$t("Failed")
            );
          }
          NProgress.done();
          this.loading = false;
        })
        .catch(error => {
          this.makeToast(
            "danger",
            this.$t("Failed_to_authenticate_on_SMTP_server"),
            this.$t("Failed")
          );
          NProgress.done();
          this.loading = false;
        });
    }
  }
};
</script>
