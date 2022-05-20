<template>
  <v-dialog
      v-model="dialog"
      persistent
      max-width="500"
  >
    <v-card>
      <v-card-title class="text--white text-sm-h6 warning " size="12">
        {{title}}
      </v-card-title>
      <v-card-text class="text-no-wrap mt-4" >
        {{titleSub}}
      </v-card-text>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
            color="green darken-1"
            text
            @click="closeDialog()"
        >
          Đóng
        </v-btn>
        <v-btn
            color="green darken-1"
            text
            @click="$emit('clickAccept')"
        >
          Đồng ý
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
export default {
  name: "DialogConfirm",
  props: ['dialog', 'title', 'titleSub'],
  emits: ['clickAccept', 'close'],
  data() {
    return {

    }
  },
  methods: {
    closeDialog () {
      // this.dialog = false
      this.$emit('close');
    }
  }
}
</script>

<style scoped>

</style>