import { Button, Card, CardBody, CardFooter, CardHeader, __experimentalHeading as Heading } from '@wordpress/components';
import { useConfigStore } from '../../store/configStore';

const SECTIONS = [
    { type: 'slider', label: 'Slider' },
    { type: 'categoryGrid', label: 'Category Grid' },
    { type: 'productList', label: 'Product List' },
    { type: 'postList', label: 'Post List' },
];

export function SectionLibrary({
    onAdd,
}: {
    onAdd: (type: any) => void;
}) {
    const { save } = useConfigStore();
    return (
        <Card>
            <CardHeader><Heading level={4}>Sections</Heading></CardHeader>
            <CardBody>
                {SECTIONS.map((s) => (
                    <Button
                        key={s.type}
                        variant="secondary"
                        style={{ width: '100%', marginBottom: 8 }}
                        onClick={() => onAdd(s.type)}
                    >
                        + {s.label}
                    </Button>
                ))}
            </CardBody>
            <CardFooter>
                <Button
                    variant="primary"
                    onClick={save}
                    style={{
                        width: '100%',
                        justifyContent: 'center',
                        textAlign: 'center',
                    }}
                >
                    Save Changes
                </Button>
            </CardFooter>
        </Card>
    );
}
