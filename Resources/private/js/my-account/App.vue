<template>
    <div>
        <div class="row">
            <div class="col-12 col-sm-6 col-md-8 col-lg-6 col-xl-8">
                <b-form @submit.prevent="updateProfile">
                    <b-card border-variant="none" bg-variant="transparent" :header="$t.trans('my_account.title.account_settings')">
                        <b-form-group>
                            <label for="form-email">{{ $t.trans('form.label.email') }}</label>
                            <b-form-input id="form-email" v-model="user.data.email" required
                                          type="email"></b-form-input>
                        </b-form-group>

                        <b-form-group>
                            <label for="form-name">{{ $t.trans('my_account.form.label.name') }}</label>
                            <b-form-input id="form-name" v-model="user.data.name" required type="text"></b-form-input>
                        </b-form-group>

                        <div slot="footer">
                            <b-button type="submit" variant="primary" :disabled="!dataLoaded || user.loading">
                                {{ $t.trans('action.save') }}
                            </b-button>
                        </div>
                    </b-card>
                </b-form>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg-6 col-xl-4">
                <b-form @submit.prevent="updatePassword">
                    <b-card :header="$t.trans('my_account.title.change_password')" footer-tag="footer">
                        <b-form-group>
                            <label for="form-current-password">{{ $t.trans('my_account.form.label.current_password') }}</label>
                            <b-form-input id="form-current-password" v-model="password.data.oldPassword" required
                                          type="password" :state="password.errorState('oldPassword')"></b-form-input>
                            <b-form-invalid-feedback v-for="(error, i) in password.getErrors('oldPassword')" :key="i">{{
                                error }}
                            </b-form-invalid-feedback>
                        </b-form-group>

                        <b-form-group>
                            <label for="form-new-password">{{ $t.trans('my_account.form.label.new_password') }}</label>
                            <b-form-input id="form-new-password" v-model="password.data.password" required
                                          type="password" :state="password.errorState('password')"></b-form-input>
                        </b-form-group>

                        <b-form-group>
                            <b-form-input v-model="password.data.repeat" required type="password"
                                          :state="password.errorState('password')"></b-form-input>
                            <b-form-invalid-feedback v-for="(error, i) in password.getErrors('password')" :key="i">{{
                                error }}
                            </b-form-invalid-feedback>
                        </b-form-group>

                        <div slot="footer">
                            <b-button type="submit" variant="primary"
                                      :disabled="password.data.password !== password.data.repeat || password.loading">
                                {{ $t.trans('action.save') }}
                            </b-button>
                        </div>
                    </b-card>
                </b-form>
            </div>
        </div>
    </div>
</template>

<script>
  export default {
    data() {
      return {
        user: new $Scriber.form(['email', 'name'], true, 1500),
        password: new $Scriber.form(['password', 'repeat', 'oldPassword'], true, 1500),
        dataLoaded: false
      }
    },
    mounted() {
      this.$http
        .get(
          $Scriber.getUrl('/api/my-account')
        )
        .then(response => {
          this.user.data.email = response.data.email
          this.user.data.name = response.data.name
          this.dataLoaded = true
        })
    },
    methods: {
      updateProfile() {
        this.user.submit(
          $Scriber.getUrl('/api/my-account'),
          'PUT'
        )
      },
      updatePassword() {
        if (this.password.data.password !== this.password.data.repeat) {
          return;
        }

        this.password.submit(
          $Scriber.getUrl('/api/my-account/password'),
          'PUT',
          {
            password: 'password',
            oldPassword: 'oldPassword'
          }
        )
      }
    }
  }
</script>
