<?php

wp_enqueue_media();
wp_enqueue_script("Media Uploader", get_template_directory_uri() . "/js/wp_media.js");

function render_alert(Page_Creator $_this)
{
    if ($_this->error !== NULL) {
        ?>
        <div class="error notice is-dismissable">
            <p><?= $_this->error_message ?></p>
        </div>
        <?php
    } else if ($_this->success !== NULL) {
        ?>
        <div class="updated notice is-dismissable">
            <p>The <?= $_this->parent->schema["resource"] ?> was successfully uploaded!</p>
        </div>
        <?php
    }
}

?>

<div class="wrap">

    <h1><?= $this->get_page_name("add") ?></h1>

    <?php

    render_alert($this);

    ?>

    <form action="" method="post">
        <input type="hidden" name="action" value="create">
        <table class="form-table" role="presentation">
            <tbody>

                <?php

                foreach ($this->schema as $field => $args) {
                    
                    $this->create_input_element($field, $args);

                }

                ?>

                <tr>
                    <th>
                        <button
                            id="<?= "Add" . $this->args["resource"] . "Submit" ?>"
                            type="submit"
                            class="button-primary"
                        >
                            Submit
                        </button>
                    </th>
                </tr>

            </tbody>
        </table>
    </form>

</div>

<?php

?>